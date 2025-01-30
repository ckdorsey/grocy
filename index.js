
const express = require('express');
const { exec } = require('child_process');
const path = require('path');
const app = express();
const port = 3000;

// Serve static files
app.use('/public', express.static('public'));
app.use('/viewjs', express.static('public/viewjs'));
app.use('/css', express.static('public/css'));
app.use('/packages', express.static('public/packages'));

// Handle PHP files and routing
app.use('/', (req, res) => {
  const phpScript = req.path === '/' ? '/public/index.php' : path.join(process.cwd(), req.path);
  const cmd = `php ${phpScript}`;
  
  exec(cmd, {
    env: {
      ...process.env,
      REQUEST_URI: req.url,
      REQUEST_METHOD: req.method,
      QUERY_STRING: req._parsedUrl.query || '',
      DOCUMENT_ROOT: process.cwd()
    }
  }, (error, stdout, stderr) => {
    if (error) {
      console.error(`Error: ${error}`);
      res.status(500).send(error.message);
      return;
    }
    res.send(stdout);
  });
});

app.listen(port, '0.0.0.0', () => {
  console.log(`Server running at http://0.0.0.0:${port}`);
});
