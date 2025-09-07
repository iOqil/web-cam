const WebSocket = require('ws');
const http = require('http');

const STREAM_PORT = 8082;   // ffmpeg shu portga yuboradi
const WEBSOCKET_PORT = 9999;

const streamServer = http.createServer((req, res) => {
  res.connection.setTimeout(0);
  req.on('data', (data) => {
    wsServer.clients.forEach((client) => {
      if (client.readyState === WebSocket.OPEN) {
        client.send(data);
      }
    });
  });
});

streamServer.listen(STREAM_PORT);
console.log(`📡 Stream server started on http://127.0.0.1:${STREAM_PORT}/stream`);

const wsServer = new WebSocket.Server({ port: WEBSOCKET_PORT, perMessageDeflate: false });
console.log(`🚀 WebSocket server started on ws://127.0.0.1:${WEBSOCKET_PORT}`);
