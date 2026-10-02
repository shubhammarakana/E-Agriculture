import json
import os
from http.server import BaseHTTPRequestHandler, HTTPServer
import time

faqs = []
kb_path = os.path.join(os.path.dirname(__file__), 'kb_data.json')
if os.path.exists(kb_path):
    try:
        with open(kb_path, 'r', encoding='utf-8') as f:
            data = json.load(f)
            for item in data:
                faqs.append({
                    'keywords': item.get('keywords', []),
                    'answer': item.get('answer', "")
                })
        print(f"Loaded {len(faqs)} FAQs in Python server.")
    except Exception as e:
        print(f"Error loading FAQs: {e}")

class ChatbotHandler(BaseHTTPRequestHandler):
    def do_OPTIONS(self):
        self.send_response(200, "ok")
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        self.end_headers()

    def do_POST(self):
        if self.path == "/api/chat":
            content_length = int(self.headers['Content-Length'])
            post_data = self.rfile.read(content_length)
            
            try:
                data = json.loads(post_data.decode('utf-8'))
                user_message = data.get('message', '').lower().strip()
                session_id = data.get('session_id', 'unknown')
                
                response_text = "Hello! I am the AgriAI Agriculture Expert. How can I assist you with your crops, soil health, or farming needs today? 🌾"
                
                faq_matched = False
                for faq in faqs:
                    for keyword in faq['keywords']:
                        if keyword.lower() in user_message:
                            response_text = faq['answer']
                            faq_matched = True
                            break
                    if faq_matched:
                        break
                
                if not faq_matched:
                    if any(word in user_message for word in ['hello', 'hi', 'hey']):
                        response_text = "Hello! I am the AgriAI Agriculture Expert. How can I assist you with your crops, soil health, or farming needs today? 🌾"
                    elif any(word in user_message for word in ['weather', 'rain']):
                        response_text = "You can check the live weather forecasts and smart irrigation advisories for your region directly on our weather page! Would you like me to fetch the current conditions for you?"
                    elif any(word in user_message for word in ['price', 'predict', 'market']):
                        response_text = "Our AI price prediction tool uses historical market data to forecast crop prices. You can check the 'Market Trends' page for details, or I can tell you the average listing prices right now."
                    elif any(word in user_message for word in ['password', 'login']):
                        response_text = "If you forgot your password, you can use the 'Forgot Password' link on the login page to securely reset it using your email address."
                    elif any(word in user_message for word in ['contact', 'support']):
                        response_text = "You can reach our human support team at support@agriai.com or call us at +91 123 456 7890."

                # Simulate AI delay
                time.sleep(1.0)
                
                response_data = {
                    'response': response_text,
                    'session_id': session_id
                }
                
                self.send_response(200)
                self.send_header('Content-Type', 'application/json')
                self.send_header('Access-Control-Allow-Origin', '*')
                self.end_headers()
                self.wfile.write(json.dumps(response_data).encode('utf-8'))
                
            except json.JSONDecodeError:
                self.send_response(400)
                self.end_headers()
        else:
            self.send_response(404)
            self.end_headers()

def run_server(port=8000):
    server_address = ('', port)
    httpd = HTTPServer(server_address, ChatbotHandler)
    print(f"Starting AgriAI Python Chatbot Server on port {port}...")
    print("Press Ctrl+C to stop.")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        pass
    httpd.server_close()
    print("Server stopped.")

if __name__ == '__main__':
    run_server()
