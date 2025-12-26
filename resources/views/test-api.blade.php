<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .result {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background: #45a049;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <h1>API Test Page</h1>
    <p>Testing if the Tags API is working correctly</p>

    <button onclick="testTagsAPI()">Test /api/tags</button>
    <button onclick="testGamesAPI()">Test /api/games</button>

    <div id="results"></div>

    <script>
        async function testTagsAPI() {
            const results = document.getElementById('results');
            results.innerHTML = '<div class="result">Testing /api/tags...</div>';

            try {
                const response = await fetch('/api/tags');
                const data = await response.json();

                results.innerHTML = `
                    <div class="result success">
                        <h3>✓ API Response Success</h3>
                        <p><strong>Status:</strong> ${response.status}</p>
                        <p><strong>Response Structure:</strong></p>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                        <p><strong>Tags Count:</strong> ${data.data ? data.data.length : 'N/A'}</p>
                        ${data.data ? `
                            <p><strong>Tag Names:</strong></p>
                            <ul>
                                ${data.data.map(tag => `<li>${tag.name} (${tag.slug})</li>`).join('')}
                            </ul>
                        ` : ''}
                    </div>
                `;
            } catch (err) {
                results.innerHTML = `
                    <div class="result error">
                        <h3>✗ API Request Failed</h3>
                        <p><strong>Error:</strong> ${err.message}</p>
                        <pre>${err.stack}</pre>
                    </div>
                `;
            }
        }

        async function testGamesAPI() {
            const results = document.getElementById('results');
            results.innerHTML = '<div class="result">Testing /api/games...</div>';

            try {
                const response = await fetch('/api/games');
                const data = await response.json();

                results.innerHTML = `
                    <div class="result success">
                        <h3>✓ Games API Response Success</h3>
                        <p><strong>Status:</strong> ${response.status}</p>
                        <p><strong>Response Structure:</strong></p>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    </div>
                `;
            } catch (err) {
                results.innerHTML = `
                    <div class="result error">
                        <h3>✗ Games API Request Failed</h3>
                        <p><strong>Error:</strong> ${err.message}</p>
                        <pre>${err.stack}</pre>
                    </div>
                `;
            }
        }

        // Auto-run test on page load
        window.addEventListener('load', function() {
            testTagsAPI();
        });
    </script>
</body>
</html>
