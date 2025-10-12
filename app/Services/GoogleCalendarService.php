<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    public function getClient()
    {
        $client = new Google_Client();
        
        // FIXED: Use google_calendar instead of google to match GoogleAuthController
        $client->setClientId(config('services.google_calendar.client_id'));
        $client->setClientSecret(config('services.google_calendar.client_secret'));
        $client->setRedirectUri(config('services.google_calendar.redirect'));
        
        // Add both read and write scopes for calendar
        $client->addScope('https://www.googleapis.com/auth/calendar');
        $client->addScope('https://www.googleapis.com/auth/calendar.events');
        
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setIncludeGrantedScopes(true);

        return $client;
    }

    public function getCalendarServiceForUser($user)
    {
        if (!$user) {
            Log::error("No user provided to getCalendarServiceForUser");
            return null;
        }

        if (!$user->google_token) {
            Log::info("User {$user->id} doesn't have a Google token");
            return null;
        }

        try {
            $client = $this->getClient();
            
            // Handle both array and JSON string formats
            $tokenData = is_array($user->google_token) 
                ? $user->google_token 
                : json_decode($user->google_token, true);
            
            if (!$tokenData) {
                Log::error("Invalid token format for user {$user->id}");
                return null;
            }
            
            $client->setAccessToken($tokenData);

            // Refresh if expired
            if ($client->isAccessTokenExpired()) {
                Log::info("Token expired for user {$user->id}, attempting refresh");
                
                $refreshToken = $client->getRefreshToken() ?? $tokenData['refresh_token'] ?? null;
                
                if ($refreshToken) {
                    $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                    
                    if (isset($newToken['error'])) {
                        Log::error("Token refresh failed for user {$user->id}: " . $newToken['error']);
                        return null;
                    }
                    
                    // Preserve refresh token if not included in new token
                    if (!isset($newToken['refresh_token']) && isset($tokenData['refresh_token'])) {
                        $newToken['refresh_token'] = $tokenData['refresh_token'];
                    }
                    
                    $user->google_token = $newToken;
                    $user->google_token_expires_at = Carbon::now()->addSeconds($newToken['expires_in'] ?? 3600);
                    $user->save();
                    
                    Log::info("Token refreshed successfully for user {$user->id}");
                } else {
                    Log::error("No refresh token available for user {$user->id}");
                    return null;
                }
            }

            return new Google_Service_Calendar($client);
            
        } catch (\Exception $e) {
            Log::error("Failed to get calendar service for user {$user->id}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Get user's primary calendar events for a date range
     */
    public function getEventsForDateRange($user, $startDate, $endDate)
    {
        $service = $this->getCalendarServiceForUser($user);
        
        if (!$service) {
            return [];
        }

        try {
            $optParams = [
                'timeMin' => $startDate->toISOString(),
                'timeMax' => $endDate->toISOString(),
                'singleEvents' => true,
                'orderBy' => 'startTime',
            ];

            $results = $service->events->listEvents('primary', $optParams);
            return $results->getItems();
            
        } catch (\Exception $e) {
            Log::error("Failed to get events for user {$user->id}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if a user has any conflicts for a given time slot
     */
    public function hasConflict($user, $startDateTime, $endDateTime)
    {
        $service = $this->getCalendarServiceForUser($user);
        
        if (!$service) {
            return false; // If no calendar access, assume no conflict
        }

        try {
            $optParams = [
                'timeMin' => $startDateTime->toISOString(),
                'timeMax' => $endDateTime->toISOString(),
                'singleEvents' => true,
            ];

            $results = $service->events->listEvents('primary', $optParams);
            $events = $results->getItems();

            return count($events) > 0;
            
        } catch (\Exception $e) {
            Log::error("Failed to check conflicts for user {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get authorization URL for Google Calendar access
     */
    public function getAuthorizationUrl($state = null)
    {
        $client = $this->getClient();
        
        if ($state) {
            $client->setState($state);
        }
        
        return $client->createAuthUrl();
    }

    /**
     * Handle the callback from Google OAuth
     */
    public function handleCallback($code, $user)
    {
        try {
            $client = $this->getClient();
            $token = $client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                Log::error("OAuth callback error: " . $token['error']);
                return false;
            }
            
            $user->google_token = $token; // Store as array, will be cast to JSON
            $user->google_token_expires_at = Carbon::now()->addSeconds($token['expires_in'] ?? 3600);
            $user->save();
            
            Log::info("Google Calendar connected successfully for user {$user->id}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("OAuth callback failed for user {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Disconnect Google Calendar for a user
     */
    public function disconnect($user)
    {
        try {
            if ($user->google_token) {
                $client = $this->getClient();
                $tokenData = is_string($user->google_token) 
                    ? json_decode($user->google_token, true) 
                    : $user->google_token;
                
                if ($tokenData && isset($tokenData['access_token'])) {
                    $client->setAccessToken($tokenData);
                    $client->revokeToken();
                }
            }
            
            $user->google_token = null;
            $user->google_token_expires_at = null;
            $user->save();
            
            Log::info("Google Calendar disconnected for user {$user->id}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to disconnect Google Calendar for user {$user->id}: " . $e->getMessage());
            
            // Even if revocation fails, clear the token
            $user->google_token = null;
            $user->google_token_expires_at = null;
            $user->save();
            
            return true;
        }
    }

    /**
     * Create a Google Calendar event
     * Helper method for creating events with standard format
     */
    public function createEvent($user, $eventData)
    {
        $service = $this->getCalendarServiceForUser($user);
        
        if (!$service) {
            throw new \Exception("Could not get Google Calendar service for user");
        }

        try {
            $event = new Google_Service_Calendar_Event($eventData);
            
            $createdEvent = $service->events->insert('primary', $event, [
                'sendUpdates' => 'all'
            ]);
            
            Log::info("Google Calendar event created with ID: " . $createdEvent->getId());
            
            return $createdEvent;
            
        } catch (\Exception $e) {
            Log::error("Failed to create Google Calendar event: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update a Google Calendar event
     */
    public function updateEvent($user, $eventId, $eventData)
    {
        $service = $this->getCalendarServiceForUser($user);
        
        if (!$service) {
            throw new \Exception("Could not get Google Calendar service for user");
        }

        try {
            $event = $service->events->get('primary', $eventId);
            
            // Update event properties
            foreach ($eventData as $key => $value) {
                $setter = 'set' . ucfirst($key);
                if (method_exists($event, $setter)) {
                    $event->$setter($value);
                }
            }
            
            $updatedEvent = $service->events->update('primary', $eventId, $event, [
                'sendUpdates' => 'all'
            ]);
            
            Log::info("Google Calendar event updated: " . $eventId);
            
            return $updatedEvent;
            
        } catch (\Exception $e) {
            Log::error("Failed to update Google Calendar event {$eventId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a Google Calendar event
     */
    public function deleteEvent($user, $eventId)
    {
        $service = $this->getCalendarServiceForUser($user);
        
        if (!$service) {
            throw new \Exception("Could not get Google Calendar service for user");
        }

        try {
            $service->events->delete('primary', $eventId, [
                'sendUpdates' => 'all'
            ]);
            
            Log::info("Google Calendar event deleted: " . $eventId);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to delete Google Calendar event {$eventId}: " . $e->getMessage());
            throw $e;
        }
    }
}