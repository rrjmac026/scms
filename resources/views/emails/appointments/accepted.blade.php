<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6; 
            color: #1a1a1a;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 40px 20px;
        }
        
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .header { 
            background: linear-gradient(135deg, #FF6B9D 0%, #C644FC 100%);
            padding: 50px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .logo-container { 
            width: 100px; 
            height: 100px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
            padding: 15px;
        }
        
        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .header h2 { 
            color: white; 
            font-size: 24px; 
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }
        
        .header h3 { 
            color: rgba(255, 255, 255, 0.95); 
            font-size: 16px; 
            font-weight: 400;
            position: relative;
            z-index: 1;
        }
        
        .content { 
            padding: 45px 40px;
            background: white;
        }
        
        .greeting {
            font-size: 20px;
            color: #2d3748;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        
        .status-badge::before {
            content: '✓';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            margin-right: 8px;
            font-weight: bold;
        }
        
        .message-text {
            color: #4a5568;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.7;
        }
        
        .appointment-card { 
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 30px;
            border-radius: 16px;
            margin: 30px 0;
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }
        
        .appointment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #FF6B9D 0%, #C644FC 100%);
        }
        
        .appointment-card h4 {
            color: #1a202c;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .detail-row {
            display: flex;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #FF6B9D 0%, #C644FC 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
            font-size: 18px;
        }
        
        .detail-content {
            flex: 1;
            padding-top: 4px;
        }
        
        .detail-label {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .detail-value {
            font-size: 16px;
            color: #2d3748;
            font-weight: 600;
        }
        
        .reminder-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 16px 20px;
            border-radius: 8px;
            margin: 25px 0;
            display: flex;
            align-items: center;
        }
        
        .reminder-box::before {
            content: '⚠️';
            font-size: 24px;
            margin-right: 12px;
        }
        
        .reminder-box p {
            color: #92400e;
            font-size: 14px;
            margin: 0;
            font-weight: 500;
        }
        
        .btn-container {
            text-align: center;
            margin: 35px 0 20px;
        }
        
        .btn { 
            display: inline-block; 
            padding: 16px 40px;
            background: linear-gradient(135deg, #FF6B9D 0%, #C644FC 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 10px 25px rgba(255, 107, 157, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(255, 107, 157, 0.5);
        }
        
        .footer { 
            text-align: center;
            padding: 30px 40px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer p {
            font-size: 13px;
            color: #718096;
            margin-bottom: 8px;
            line-height: 1.6;
        }
        
        .footer p:last-child {
            margin-bottom: 0;
        }
        
        @media only screen and (max-width: 600px) {
            body { padding: 20px 10px; }
            .content { padding: 30px 25px; }
            .header { padding: 40px 25px; }
            .appointment-card { padding: 20px; }
            .detail-row { flex-direction: column; }
            .detail-icon { margin-bottom: 10px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <div class="logo-container">
                <img src="{{ $message->embed(public_path('assets/app_logo.PNG')) }}" alt="LCCDO Logo">
            </div>
            <h2>Lourdes College of Cagayan de Oro City</h2>
            <h3>Student Counseling Management System</h3>
        </div>
        
        <div class="content">
            <p class="greeting">Hello {{ $appointment->student->user->first_name }}! 👋</p>
            
            <div class="status-badge">Appointment Accepted</div>
            
            <p class="message-text">
                Great news! Your counseling appointment request has been accepted by <strong>{{ $appointment->counselor->user->name }}</strong>. We're looking forward to meeting with you.
            </p>

            <div class="appointment-card">
                <h4>📅 Appointment Details</h4>
                
                <div class="detail-row">
                    <div class="detail-icon">📅</div>
                    <div class="detail-content">
                        <div class="detail-label">Date</div>
                        <div class="detail-value">{{ $appointment->preferred_date->format('F d, Y') }}</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon">🕐</div>
                    <div class="detail-content">
                        <div class="detail-label">Time</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($appointment->preferred_time)->format('h:i A') }}</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon">📍</div>
                    <div class="detail-content">
                        <div class="detail-label">Location</div>
                        <div class="detail-value">Guidance Office</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon">👤</div>
                    <div class="detail-content">
                        <div class="detail-label">Counselor</div>
                        <div class="detail-value">{{ $appointment->counselor->user->name }}</div>
                    </div>
                </div>
            </div>

            <div class="reminder-box">
                <p>Please arrive 5 minutes before your scheduled time to ensure a smooth check-in process.</p>
            </div>
            
            <div class="btn-container">
                <a href="{{ route('student.appointments.show', $appointment) }}" class="btn">View Full Details →</a>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated message from the LCCDO Student Counseling Management System.</p>
            <p><strong>Need help?</strong> Contact the Guidance Office for any questions or concerns.</p>
            <p>© {{ date('Y') }} Lourdes College of Cagayan de Oro City. All rights reserved.</p>
        </div>
    </div>
</body>
</html>