#!/usr/bin/env python3
from http.server import BaseHTTPRequestHandler, HTTPServer
import re
from urllib.parse import urlparse, parse_qs

HOST = "localhost"
PORT = 8080
 
class MyServer(BaseHTTPRequestHandler):
    def do_GET(self):
        self.send_response(200)
        self.send_header("Content-type", "text/html; charset=utf-8")
        self.end_headers()
    
        try:
            with open("webovky/content.html", "rb") as f:
                self.wfile.write(f.read())

                #query parsing code
                parsed_url = urlparse(self.path)
                query_params = parse_qs(parsed_url.query)

                name = query_params.get('name', ['-_-'])[0]
                age = query_params.get('age', ['-_-'])[0]
                self.wfile.write(bytes(f"<p>{name} is {age} years old.</p>" , "utf-8"))
                
        except:
            self.wfile.write(bytes("<p>Response: 200 </p>" , "utf-8"))

if __name__ == "__main__":        
    server = HTTPServer((HOST, PORT), MyServer)
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        pass
    server.server_close()