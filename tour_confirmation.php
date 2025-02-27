<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tour Confirmation</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(to right, #434343, #000000);
            color: white;
            margin: 0;
            padding: 0;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        .navbar {
            background-color: black;
            color: orange;
            padding: 15px;
            text-align: center;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1;
        }
        .navbar a {
            color: orange;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 1em;
        }
        .navbar a:hover {
            background-color: #444;
        }
        .confirmation-message {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            animation: bounceIn 2s ease-out, pulse 1.5s infinite;
        }
        .confirmation-message h1 {
            margin: 0;
            font-size: 2.5em;
            animation: textFadeIn 2s ease-out;
        }
        .confirmation-message p {
            font-size: 1.5em;
            animation: textFadeIn 2s ease-out 1s;
        }
        @keyframes bounceIn {
            from {
                opacity: 0;
                transform: scale(0.5);
            }
            50% {
                opacity: 1;
                transform: scale(1.2);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        @keyframes textFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.html">Home</a>
    <a href="TouristLogin.html">Login Page</a>
    <a href="TouristInterface.html">Interface Page</a>
</div>

<div class="confirmation-message">
    <h1>Thank You!</h1>
    <p>Your tour plan has been successfully confirmed and stored. We appreciate your booking!</p>
</div>

</body>
</html>
