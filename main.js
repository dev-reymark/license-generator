const { app, BrowserWindow } = require("electron");
const path = require("path");
const { spawn } = require("child_process");

let phpServer;

function createWindow() {
  const win = new BrowserWindow({
    width: 800,
    height: 700,
    webPreferences: {
      contextIsolation: true,
    },
  });

  // Load your PHP server output in the Electron window
  win.loadURL("http://127.0.0.1:8000");
}

app.whenReady().then(() => {
  // ✅ Start bundled PHP runtime
  const phpPath = path.join(__dirname, "php-runtime", "php.exe");
  const phpRoot = path.join(__dirname, "php");

  phpServer = spawn(phpPath, ["-S", "127.0.0.1:8000", "-t", phpRoot], {
    windowsHide: true,
  });

  phpServer.stdout.on("data", (data) => console.log(`[PHP]: ${data}`));
  phpServer.stderr.on("data", (data) => console.error(`[PHP Error]: ${data}`));

  createWindow();

  app.on("activate", () => {
    if (BrowserWindow.getAllWindows().length === 0) createWindow();
  });
});

app.on("window-all-closed", () => {
  if (phpServer) phpServer.kill(); // ❌ Stop the PHP server when Electron quits
  if (process.platform !== "darwin") app.quit();
});
