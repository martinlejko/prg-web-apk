<!DOCTYPa ideE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JSON Post Example</title>
    <style>
        .response {
            margin-top: 20px;
            font-size: 20px;
            color: #333;
        }
    </style>
</head>
<body>
    <button id="submitButton">Submit Data</button>
    
    <dl>
        <dd id="personName"></dd>
        <dd id="personPosition"></dd>
    </dl>

    <script>
document.getElementById('submitButton').addEventListener('click', function() {
    fetch('10-json-processing.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name: "Pavel", position: "Teacher" })
    })
    .then(response => response.json())
    .then(data => {
    
        document.getElementById('personName').textContent = data.name;
        
        document.getElementById('personPosition').textContent = data.position;
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
        document.getElementById('submitButton').addEventListener('click', function() {
    fetch('10-json-processing.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name: "Pavel", position: "Teacher" })
    })
    .then(response => response.json())
    .then(data => {
        
        var readableResponse = "Status: " + data.status + "\nName: " + data.data.name + "\nPosition: " + data.data.position;
        document.getElementById('response').textContent = readableResponse;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('response').textContent = 'Error processing request';
    });
});

    </script>
</body>
</html>