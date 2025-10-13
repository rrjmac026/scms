<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    public function getClient()
    {
        $client = new Google_Client();
        
        $client->setClientId(config('services.google_calendar.client_id'));
        $client->setClientSecret(config('services.google_calendar.client_secret'));
        $client->setRedirectUri(config('services.google_calendar.redirect'));
        
        $client->addScope('https://www.googleapis.com/auth/calendar');
        $client->addScope('https://www.googleapis.com/auth/calendar.events');
        
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setIncludeGrantedScopes(true);

        return $client;
    }

    public function getCalendarServiceForUser($user)
    {
        Log::info("=== GET CALENDAR SERVICE STARTED ===");
        Log::info("User ID: {$user->id}");
        
        if (!$user) {
            Log::error("No user provided to getCalendarServiceForUser");
            return null;
        }

        if (!$user->google_token) {
            Log::error("User {$user->id} doesn't have a Google token");
            return null;
        }

        Log::info("User has google_token");

        try {
            $client = $this->getClient();
            Log::info("Google Client created");
            
            $tokenData = is_array($user->google_token) 
                ? $user->google_token 
                : json_decode($user->google_token, true);
            
            if (!$tokenData) {
                Log::error("Invalid token format for user {$user->id}");
                return null;
            }
            
            Log::info("Token data decoded successfully");
            
            $client->setAccessToken($tokenData);
            Log::info("Access token set on client");

            if ($client->isAccessTokenExpired()) {
                Log::warning("Token expired for user {$user->id}, attempting refresh");
                
                $refreshToken = $client->getRefreshToken() ?? $tokenData['refresh_token'] ?? null;
                
                if ($refreshToken) {
                    Log::info("Refresh token found, fetching new access token");
                    $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                    
                    if (isset($newToken['error'])) {
                        Log::error("Token refresh failed for user {$user->id}: " . $newToken['error']);
                        return null;
                    }
                    
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
            } else {
                Log::info("Token is still valid, no refresh needed");
            }

            $service = new Google_Service_Calendar($client);
            Log::info("Google_Service_Calendar created successfully");
            Log::info("=== GET CALENDAR SERVICE COMPLETED ===");
            
            return $service;
            
        } catch (\Exception $e) {
            Log::error("=== GET CALENDAR SERVICE FAILED ===");
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
            return false;
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
            
            $user->google_token = $token;
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
            
            $user->google_token = null;
            $user->google_token_expires_at = null;
            $user->save();
            
            return true;
        }
    }

    /**
     * Create a Google Calendar event
     */
    public function createEvent($user, $eventData)
    {
        Log::info("=== CREATE EVENT STARTED ===");
        Log::info("User ID: {$user->id}");
        Log::info("Event data:", $eventData);
        
        $service = $this->getCalendarServiceForUser($user);
        
        if (!$service) {
            Log::error("Could not get Google Calendar service for user {$user->id}");
            throw new \Exception("Could not get Google Calendar service for user");
        }

        Log::info("Calendar service obtained successfully");

        try {
            Log::info("Creating Google_Service_Calendar_Event object");
            $event = new Google_Service_Calendar_Event($eventData);
            
            Log::info("Inserting event into primary calendar");
            $createdEvent = $service->events->insert('primary', $event, [
                'sendUpdates' => 'all'
            ]);
            
            $eventId = $createdEvent->getId();
            Log::info("Google Calendar event created successfully with ID: {$eventId}");
            
            if (!$eventId) {
                Log::error("Event was created but ID is null!");
                throw new \Exception("Event created but no ID returned");
            }
            
            Log::info("=== CREATE EVENT COMPLETED ===");
            return $createdEvent;
            
        } catch (\Exception $e) {
            Log::error("=== CREATE EVENT FAILED ===");
            Log::error("Error message: " . $e->getMessage());
            Log::error("Error code: " . $e->getCode());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Update a Google Calendar event
     * 🔧 FIXED: Properly handle Google Calendar objects
     */
    public function updateEvent($user, $eventId, $eventData)
    {
        Log::info("=== UPDATE EVENT STARTED ===");
        Log::info("User ID: {$user->id}, Event ID: {$eventId}");
        
        $service = $this->getCalendarServiceForUser($user);
        
        if (!$service) {
            throw new \Exception("Could not get Google Calendar service for user");
        }

        try {
            // Get the existing event
            $event = $service->events->get('primary', $eventId);
            Log::info("Retrieved existing event");
            
            // Update summary (title)
            if (isset($eventData['summary'])) {
                $event->setSummary($eventData['summary']);
            }
            
            // Update description
            if (isset($eventData['description'])) {
                $event->setDescription($eventData['description']);
            }
            
            // Update start time
            if (isset($eventData['start'])) {
                $start = new Google_Service_Calendar_EventDateTime();
                if (isset($eventData['start']['dateTime'])) {
                    $start->setDateTime($eventData['start']['dateTime']);
                    $start->setTimeZone($eventData['start']['timeZone'] ?? 'Asia/Manila');
                } elseif (isset($eventData['start']['date'])) {
                    $start->setDate($eventData['start']['date']);
                }
                $event->setStart($start);
            }
            
            // Update end time
            if (isset($eventData['end'])) {
                $end = new Google_Service_Calendar_EventDateTime();
                if (isset($eventData['end']['dateTime'])) {
                    $end->setDateTime($eventData['end']['dateTime']);
                    $end->setTimeZone($eventData['end']['timeZone'] ?? 'Asia/Manila');
                } elseif (isset($eventData['end']['date'])) {
                    $end->setDate($eventData['end']['date']);
                }
                $event->setEnd($end);
            }
            
            // Update location
            if (isset($eventData['location'])) {
                $event->setLocation($eventData['location']);
            }
            
            // Update attendees
            if (isset($eventData['attendees'])) {
                $event->setAttendees($eventData['attendees']);
            }
            
            // Send the update
            $updatedEvent = $service->events->update('primary', $eventId, $event, [
                'sendUpdates' => 'all'
            ]);
            
            Log::info("✅ Google Calendar event updated successfully: " . $eventId);
            Log::info("=== UPDATE EVENT COMPLETED ===");
            
            return $updatedEvent;
            
        } catch (\Exception $e) {
            Log::error("=== UPDATE EVENT FAILED ===");
            Log::error("Failed to update Google Calendar event {$eventId}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Delete a Google Calendar event
     */
    public function deleteEvent($user, $eventId)
    {
        Log::info("=== DELETE EVENT STARTED ===");
        Log::info("User ID: {$user->id}, Event ID: {$eventId}");
        
        $service = $this->getCalendarServiceForUser($user);
        
        if (!$service) {
            throw new \Exception("Could not get Google Calendar service for user");
        }

        try {
            $service->events->delete('primary', $eventId, [
                'sendUpdates' => 'all'
            ]);
            
            Log::info("✅ Google Calendar event deleted: " . $eventId);
            Log::info("=== DELETE EVENT COMPLETED ===");
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("=== DELETE EVENT FAILED ===");
            Log::error("Failed to delete Google Calendar event {$eventId}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
}