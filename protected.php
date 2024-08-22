<!DOCTYPE html>
<html>
<head>
    <title>FBI Database</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
            animation: header-animation 1s;
        }
        @keyframes header-animation {
            0% {
                opacity: 0;
                transform: translateY(-50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 18px;
        }
        .buttons {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .buttons a {
            margin-right: 20px;
            width: 50%;
            margin-bottom: 20px;
            animation: button-animation 1s;
            animation-fill-mode: backwards;
        }
        .buttons a:nth-child(1) {
            animation-delay: 0.1s;
        }
        .buttons a:nth-child(2) {
            animation-delay: 0.2s;
        }
        .buttons a:nth-child(3) {
            animation-delay: 0.3s;
        }
        .buttons a:nth-child(4) {
            animation-delay: 0.4s;
        }
        .buttons a:nth-child(5) {
            animation-delay: 0.5s;
        }
        .buttons a:nth-child(6) {
            animation-delay: 0.6s;
        }
        @keyframes button-animation {
            0% {
                opacity: 0;
                transform: translateX(-50px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FBI Database</h1>
        <p>Welcome, <?php session_start(); echo $_SESSION["username"]; ?>!</p>
    </div>
    <div class="buttons">
        <a href="manage_countries.php" class="btn btn-primary">Manage Countries</a>
        <a href="manage_cities.php" class="btn btn-primary">Manage Cities</a>
        <a href="manage_surveillance_devices.php" class="btn btn-primary">Manage Surveillance Devices</a>
        <a href="manage_field_agents.php" class="btn btn-primary">Manage Field Agents</a>
        <a href="manage_suspects.php?search_term=&search_by=name" class="btn btn-primary">Manage Suspects</a>
        <a href="manage_suspect_assignments.php" class="btn btn-primary">Manage Suspect Assignments</a>
    </div>
</body>
</html>