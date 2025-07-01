<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --success: #4cc9f0;
            --error: #f72585;
            --text: #2b2d42;
            --text-light: #8d99ae;
            --bg: #f8f9fa;
            --card-bg: #ffffff;
            --border: #e9ecef;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text);
            background-color: var(--bg);
            padding: 2rem 1rem;
            min-height: 100vh;
        }

        .container {
            max-width: 640px;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--primary), #4895ef);
            color: white;
        }

        .header h2 {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header h2 svg {
            width: 24px;
            height: 24px;
        }

        .content {
            padding: 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            width: 100%;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
        }

        textarea,
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            transition: border 0.2s;
            resize: vertical;
        }

        textarea:focus,
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .loading {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-top: 0.5rem;
            display: none;
        }

        .loading.active {
            display: block;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            position: relative;
            animation: fadeIn 0.3s ease;
            margin: 1rem 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background-color: rgba(76, 201, 240, 0.1);
            border-left: 4px solid var(--success);
            color: var(--text);
        }

        .alert-error {
            background-color: rgba(247, 37, 133, 0.1);
            border-left: 4px solid var(--error);
            color: var(--text);
        }

        .alert-close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            cursor: pointer;
            background: none;
            border: none;
            color: inherit;
            font-size: 1.2rem;
            line-height: 1;
        }

        .qrcode-container {
            display: flex;
            justify-content: center;
            margin: 1.5rem 0;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn-group .btn {
            width: auto;
            flex: 1;
        }

        .icon {
            width: 20px;
            height: 20px;
        }

        @media (max-width: 480px) {
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                License Key Generator
            </h2>
        </div>

        <div class="content">
            <button type="button" id="fetchBtn" class="btn btn-outline" onclick="fetchFingerprint()">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Auto-Fetch Fingerprint
            </button>
            <form id="decryptForm">
                <div class="form-group">
                    <label for="encFile">Upload Encrypted Fingerprint (.enc)</label>
                    <input type="file" id="encFile" accept=".enc,.txt" required>
                </div>
                <button type="submit" class="btn btn-outline">
                    🔓 Decrypt & Load Fingerprint
                </button>
            </form>
            <div id="loading" class="loading">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Fetching device fingerprint...
            </div>

            <form method="post" action="generate_license.php">
                <div class="form-group">
                    <label for="fingerprint">Fingerprint JSON</label>
                    <textarea name="fingerprint" id="fingerprint" rows="6" required placeholder="Paste fingerprint JSON or use auto-fetch above"></textarea>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                        </svg>
                        Generate License
                    </button>
                    <button type="button" class="btn btn-outline" onclick="resetForm()">
                        ♻️ Reset
                    </button>
                </div>
            </form>

            <div id="alertContainer">
                <?php if (isset($_GET['success']) && file_exists('license.key')):
                    $license = $_SESSION['license'] ?? file_get_contents('license.key'); ?>
                    <div class="alert alert-success">
                        <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                        <strong>✅ License generated successfully!</strong>
                        <p>Your license key has been created and saved.</p>
                        <div class="btn-group">
                            <a href="license.key" download class="btn btn-outline">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download License
                            </a>
                            <button type="button" onclick="copyLicense()" class="btn btn-outline">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                                Copy License
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="generatedLicense">Encrypted License Key</label>
                        <textarea readonly id="generatedLicense" rows="4"><?= htmlspecialchars($license) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>QR Code</label>
                        <div class="qrcode-container">
                            <div id="qrcode"></div>
                        </div>
                    </div>
                <?php unset($_SESSION['license']);
                endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error">
                        <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                        <strong>❌ Error generating license</strong>
                        <p><?= htmlspecialchars($_GET['error']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function showAlert(message, type = 'error') {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `
                <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                <strong>${type === 'success' ? '✅' : '❌'} ${type === 'success' ? 'Success' : 'Error'}</strong>
                <p>${message}</p>
            `;
            document.getElementById('alertContainer').prepend(alert);
            setTimeout(() => alert.remove(), 5000);
        }

        function fetchFingerprint() {
            const fetchBtn = document.getElementById('fetchBtn');
            const loading = document.getElementById('loading');

            fetchBtn.disabled = true;
            loading.classList.add('active');

            fetchBtn.innerHTML = `
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Fetching...
            `;

            fetch('fingerprint.php')
                .then(response => {
                    if (!response.ok) throw new Error("Failed to fetch fingerprint.");
                    return response.json();
                })
                .then(data => {
                    document.getElementById('fingerprint').value = JSON.stringify(data, null, 2);
                    showAlert("Device fingerprint successfully retrieved!", "success");
                })
                .catch(error => {
                    showAlert(error.message, "error");
                })
                .finally(() => {
                    fetchBtn.disabled = false;
                    loading.classList.remove('active');
                    fetchBtn.innerHTML = `
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Auto-Fetch Fingerprint
                    `;
                });
        }

        // Remove URL parameters after 5 seconds
        if (window.location.search.includes('success') || window.location.search.includes('error')) {
            setTimeout(() => {
                const url = new URL(window.location.href);
                url.search = '';
                window.history.replaceState({}, document.title, url.toString());
            }, 5000);
        }
    </script>
    
    <script>
        document.getElementById('decryptForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const fileInput = document.getElementById('encFile');
            const file = fileInput.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('encFile', file);

            fetch('decrypt.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        showAlert(data.error, 'error');
                    } else {
                        document.getElementById('fingerprint').value = JSON.stringify(data, null, 2);
                        showAlert('Encrypted fingerprint decrypted successfully!', 'success');
                    }
                })
                .catch(() => showAlert('Error decrypting fingerprint.', 'error'));
        });
    </script>

    <script>
        function resetForm() {
            document.getElementById('encFile').value = "";
            document.getElementById('fingerprint').value = "";

            const qrcodeContainer = document.getElementById('qrcode');
            if (qrcodeContainer) qrcodeContainer.innerHTML = "";

            const licenseField = document.getElementById('generatedLicense');
            if (licenseField) licenseField.value = "";

            const alertContainer = document.getElementById('alertContainer');
            if (alertContainer) alertContainer.innerHTML = "";

            document.getElementById('decryptForm').reset();
        }
    </script>

    <!-- QR Code Generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const licenseText = document.getElementById("generatedLicense");
            const qrcodeContainer = document.getElementById('qrcode');

            if (licenseText && licenseText.value.trim() !== "") {
                QRCode.toCanvas(licenseText.value, {
                    width: 200,
                    margin: 1,
                    color: {
                        dark: '#2b2d42',
                        light: '#ffffff'
                    }
                }, function(err, canvas) {
                    if (err) {
                        console.error("QR code error:", err);
                        return;
                    }
                    qrcodeContainer.innerHTML = '';
                    qrcodeContainer.appendChild(canvas);
                });
            }
        });

        function copyLicense() {
            const textarea = document.getElementById("generatedLicense");
            if (!textarea || textarea.value.trim() === "") return;

            textarea.select();
            textarea.setSelectionRange(0, 99999);

            try {
                const success = document.execCommand("copy");
                if (success) {
                    showAlert("License key copied to clipboard!", "success");
                } else {
                    showAlert("Failed to copy license key.", "error");
                }
            } catch (err) {
                showAlert("Your browser doesn't support clipboard copying.", "error");
            }
        }
    </script>
</body>

</html>