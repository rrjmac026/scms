<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #1f2937; background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); background-color: #fce7f3; padding: 40px 20px;">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
            
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #FF92C2 0%, #ff6ba8 100%); background-color: #FF92C2; padding: 40px 30px; text-align: center; position: relative;">
                <div style="width: 100px; height: 100px; background: white; border-radius: 50%; padding: 15px; margin: 0 auto 20px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); display: inline-block;">
                    <img src="{{ $message->embed(public_path('assets/app_logo.PNG')) }}" alt="LCCDO Logo" style="width: 100%; height: 100%; display: block; object-fit: contain;">
                </div>
                <h2 style="margin: 0 0 8px 0; color: white; font-size: 22px; font-weight: 700; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">Lourdes College of Cagayan de Oro City</h2>
                <h3 style="margin: 0; color: rgba(255, 255, 255, 0.95); font-size: 14px; font-weight: 500; letter-spacing: 0.5px;">Student Counseling Management System</h3>
                <div style="height: 4px; background: rgba(255, 255, 255, 0.3); margin-top: 20px;"></div>
            </div>
            
            <!-- Content -->
            <div style="padding: 40px 30px;">
                
                <!-- Status Badge -->
                <div style="text-align: center; margin-bottom: 30px;">
                    <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); background-color: #ef4444; color: white; padding: 12px 24px; border-radius: 50px; display: inline-block; font-weight: 600; font-size: 14px; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);">
                        <span style="display: inline-block; width: 20px; height: 20px; background: rgba(255, 255, 255, 0.3); border-radius: 50%; text-align: center; line-height: 20px; font-size: 14px; margin-right: 8px; vertical-align: middle;">ⓘ</span>
                        <span style="vertical-align: middle;">Appointment Update</span>
                    </div>
                </div>

                <p style="margin: 0 0 16px 0; font-size: 18px; font-weight: 600; color: #111827;">Hello {{ $appointment->student->user->first_name }},</p>
                
                <p style="margin: 0 0 30px 0; color: #4b5563; font-size: 15px;">We regret to inform you that your counseling appointment with <strong style="color: #FF92C2;">{{ $appointment->counselor->user->name }}</strong> could not be accommodated at this time.</p>

                <!-- Original Request Details -->
                <div style="background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); background-color: #f9fafb; border: 2px solid #e5e7eb; border-radius: 12px; padding: 24px; margin: 24px 0;">
                    <h4 style="margin: 0 0 20px 0; color: #6b7280; font-size: 16px; font-weight: 700;">
                        <span style="font-size: 18px; margin-right: 8px;">📋</span>Original Appointment Request
                    </h4>
                    
                    <div style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; display: table; width: 100%;">
                        <span style="display: table-cell; font-weight: 600; color: #374151; width: 120px; font-size: 14px;">Date:</span>
                        <span style="display: table-cell; color: #6b7280; font-size: 14px;">{{ $appointment->preferred_date->format('F d, Y') }}</span>
                    </div>
                    
                    <div style="padding: 12px 0; display: table; width: 100%;">
                        <span style="display: table-cell; font-weight: 600; color: #374151; width: 120px; font-size: 14px;">Time:</span>
                        <span style="display: table-cell; color: #6b7280; font-size: 14px;">{{ \Carbon\Carbon::parse($appointment->preferred_time)->format('h:i A') }}</span>
                    </div>
                </div>

                <!-- Rejection Reason Card -->
                <div style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); background-color: #fee2e2; border: 2px solid #fca5a5; border-left: 4px solid #ef4444; border-radius: 12px; padding: 24px; margin: 24px 0;">
                    <h4 style="margin: 0 0 16px 0; color: #991b1b; font-size: 16px; font-weight: 700;">
                        <span style="font-size: 18px; margin-right: 8px;">📝</span>Reason for Declining
                    </h4>
                    <p style="margin: 0; color: #7f1d1d; font-size: 14px; line-height: 1.7; font-style: italic;">"{{ $appointment->rejection_reason }}"</p>
                </div>

                <!-- Encouragement Box -->
                <div style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); background-color: #ecfdf5; border: 2px solid #a7f3d0; border-radius: 12px; padding: 24px; margin: 24px 0; text-align: center;">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); background-color: #10b981; border-radius: 50%; margin: 0 auto 16px; display: inline-flex; align-items: center; justify-content: center; font-size: 28px;">
                        💚
                    </div>
                    <h4 style="margin: 0 0 12px 0; color: #065f46; font-size: 17px; font-weight: 700;">We're Still Here for You</h4>
                    <p style="margin: 0 0 20px 0; color: #065f46; font-size: 14px; line-height: 1.7;">We encourage you to book another appointment at a time that works better. Our counselors are committed to supporting you on your journey.</p>
                    <a href="{{ route('student.appointments.create') }}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #FF92C2 0%, #ff6ba8 100%); background-color: #FF92C2; color: white; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 15px; box-shadow: 0 4px 6px -1px rgba(255, 146, 194, 0.4);">Schedule New Appointment</a>
                </div>

                <!-- Support Info -->
                <div style="background: #dbeafe; border-left: 4px solid #3b82f6; padding: 16px 20px; border-radius: 8px; margin: 24px 0; font-size: 14px; color: #1e40af;">
                    <strong>Need Immediate Support?</strong> You can visit the Guidance Office during office hours or contact us for urgent concerns.
                </div>

            </div>
            
            <!-- Footer -->
            <div style="background: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px; line-height: 1.8;">This is an automated message from the LCCDO Student Counseling Management System.</p>
                <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.8;">© {{ date('Y') }} Lourdes College of Cagayan de Oro City. All rights reserved.</p>
            </div>
            
        </div>
    </div>
</body>
</html>