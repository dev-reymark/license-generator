<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>License Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background: #f4f4f4;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        textarea,
        button,
        input {
            width: 100%;
            padding: 10px;
            margin-top: 1rem;
            font-size: 14px;
        }

        button {
            cursor: pointer;
            background-color: #007bff;
            color: #fff;
            border: none;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-top: 1rem;
            padding: 10px 15px;
            border-radius: 5px;
            position: relative;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
        }

        .alert .close {
            position: absolute;
            right: 10px;
            top: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 18px;
        }

        .loading {
            font-style: italic;
            color: #666;
            margin-top: 10px;
        }

        #qrcode canvas {
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>🔐 License Key Generator</h2>

        <button type="button" id="fetchBtn" onclick="fetchFingerprint()">📡 Auto-Fetch Fingerprint</button>
        <div id="loading" class="loading" style="display: none;">Fetching fingerprint...</div>

        <form method="post" action="generate_license.php">
            <label for="fingerprint">Fingerprint JSON:</label>
            <textarea name="fingerprint" id="fingerprint" required placeholder="Paste fingerprint JSON or use auto-fetch..."></textarea>
            <button type="submit">🛠️ Generate License</button>
        </form>

        <div id="alertContainer">
            <?php if (isset($_GET['success']) && file_exists('license.key')):
                $license = $_SESSION['license'] ?? file_get_contents('license.key'); ?>
                <div class="alert success">
                    ✅ License generated! <a href="license.key" download>Download license.key</a>
                    <span class="close" onclick="this.parentElement.style.display='none';">&times;</span>
                </div>

                <label for="generatedLicense">🔏 Encrypted License:</label>
                <textarea readonly id="generatedLicense"><?= htmlspecialchars($license) ?></textarea>
                <button type="button" onclick="copyLicense()">📋 Copy to Clipboard</button>


                <label>📷 QR Code:</label>
                <div id="qrcode"></div>
            <?php unset($_SESSION['license']);
            endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert error">
                    ❌ Error: <?= htmlspecialchars($_GET['error']) ?>
                    <span class="close" onclick="this.parentElement.style.display='none';">&times;</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- JS -->
    <script>
        function showAlert(message, type = 'error') {
            const alert = document.createElement('div');
            alert.className = `alert ${type}`;
            alert.innerHTML = `${type === 'success' ? '✅' : '❌'} ${message}
            <span class="close" onclick="this.parentElement.remove();">&times;</span>`;
            document.getElementById('alertContainer').appendChild(alert);
            setTimeout(() => alert.remove(), 5000);
        }

        function fetchFingerprint() {
            const fetchBtn = document.getElementById('fetchBtn');
            const loading = document.getElementById('loading');
            fetchBtn.disabled = true;
            loading.style.display = 'block';

            fetch('fingerprint.php')
                .then(response => {
                    if (!response.ok) throw new Error("Failed to fetch fingerprint.");
                    return response.json();
                })
                .then(data => {
                    document.getElementById('fingerprint').value = JSON.stringify(data, null, 4);
                    showAlert("Fingerprint successfully loaded!", "success");
                })
                .catch(error => {
                    showAlert(error.message, "error");
                })
                .finally(() => {
                    loading.style.display = 'none';
                    fetchBtn.disabled = false;
                });
        }

        // Remove ?success or ?error after 5 seconds
        if (window.location.search.includes('success=1') || window.location.search.includes('error=')) {
            setTimeout(() => {
                const url = new URL(window.location.href);
                url.search = '';
                window.history.replaceState({}, document.title, url.toString());
            }, 5000);
        }
    </script>

    <!-- QR Code Generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <script>
        window.onload = function() {
            const encoded = document.getElementById("generatedLicense");
            const qrcodeContainer = document.getElementById('qrcode');

            if (encoded && encoded.value.trim() !== "") {
                QRCode.toCanvas(encoded.value, {
                    width: 250
                }, function(err, canvas) {
                    if (err) {
                        console.error("QR code error:", err);
                        return;
                    }
                    qrcodeContainer.innerHTML = ''; // Clear previous QR code
                    qrcodeContainer.appendChild(canvas); // Append generated canvas
                });
            }
        };
    </script>

    <script>
        function copyLicense() {
            const textarea = document.getElementById("generatedLicense");
            if (!textarea || textarea.value.trim() === "") return;

            textarea.select();
            textarea.setSelectionRange(0, 99999); // For mobile

            try {
                const success = document.execCommand("copy");
                if (success) {
                    showAlert("License copied to clipboard!", "success");
                } else {
                    showAlert("Failed to copy license.", "error");
                }
            } catch (err) {
                showAlert("Browser does not support copying.", "error");
            }
        }
    </script>

</body>

</html>