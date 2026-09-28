import express from 'express';
import { createServer as createViteServer } from 'vite';
import path from 'path';

async function startServer() {
  const app = express();
  const PORT = 3000;
  app.use(express.json());

  // API Routes
  app.get('/api/project-status', (req, res) => {
    res.json({
      projectName: "FtoniNuke v1.4 (Multilingual Discovery)",
      legacyVersion: "8.1 Pro (2009)",
      targetVersion: "PHP 8.3 & Global Ready",
      compliance: {
        psr4: "Certified",
        pdo: "Certified",
        i18n: "Monolingual (7 Styles)"
      },
      files: [
        { name: "LanguageManager.php", status: "Modern", size: "3kb", path: "/src/Service" },
        { name: "tr.php / en.php", status: "Dictionary", size: "2kb", path: "/lang" },
        { name: "lang_helpers.php", status: "Global", size: "1kb", path: "/src/Helper" },
        { name: "step0.html.php", status: "Selector", size: "3kb", path: "/src/Views/install" },
        { name: "app.php", status: "Config", size: "1kb", path: "/config" },
      ]
    });
  });

  // Vite middleware for development
  if (process.env.NODE_ENV !== 'production') {
    const vite = await createViteServer({
      server: { middlewareMode: true },
      appType: 'spa',
    });
    app.use(vite.middlewares);
  } else {
    const distPath = path.join(process.cwd(), 'dist');
    app.use(express.static(distPath));
    app.get('*', (req, res) => {
      res.sendFile(path.join(distPath, 'index.html'));
    });
  }

  app.listen(PORT, '0.0.0.0', () => {
    console.log(`Server running on http://localhost:${PORT}`);
  });
}

startServer();
