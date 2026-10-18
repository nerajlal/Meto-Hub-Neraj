import base64, json, os, shutil, socket, struct, subprocess, tempfile, time, urllib.request
from pathlib import Path
class Chrome:
 def __init__(self,browser=None):
  candidates=[browser,shutil.which('chromium'),shutil.which('google-chrome'),'/Applications/Chrome.app/Contents/MacOS/Google Chrome','/Applications/Google Chrome.app/Contents/MacOS/Google Chrome']
  self.browser=next((p for p in candidates if p and Path(p).is_file()),None)
  if not self.browser:raise RuntimeError('Chrome/Chromium not found; pass --browser')
 def __enter__(self):
  self.folder=tempfile.TemporaryDirectory(prefix='metohub-browser-');self.sock=None;self.ident=0
  with socket.socket() as s:s.bind(('127.0.0.1',0));self.port=s.getsockname()[1]
  self.process=subprocess.Popen([self.browser,'--headless','--disable-gpu','--disable-background-networking','--no-first-run','--remote-allow-origins=*',f'--remote-debugging-port={self.port}','--user-data-dir='+self.folder.name,'about:blank'],stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
  for _ in range(100):
   try:
    with urllib.request.urlopen(f'http://127.0.0.1:{self.port}/json',timeout=1) as r:target=next(t for t in json.load(r) if t['type']=='page')
    break
   except Exception:time.sleep(.1)
  else:raise RuntimeError('Chrome did not start')
  from urllib.parse import urlparse
  u=urlparse(target['webSocketDebuggerUrl']);self.sock=socket.create_connection((u.hostname,u.port),timeout=30)
  key=base64.b64encode(os.urandom(16)).decode();self.sock.sendall(f'GET {u.path} HTTP/1.1\r\nHost: {u.netloc}\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Key: {key}\r\nSec-WebSocket-Version: 13\r\n\r\n'.encode())
  h=b''
  while not h.endswith(b'\r\n\r\n'):h+=self.sock.recv(1)
  self.call('Page.enable');return self
 def read(self,n):
  b=b''
  while len(b)<n:
   x=self.sock.recv(n-len(b))
   if not x:raise EOFError()
   b+=x
  return b
 def call(self,method,params=None):
  self.ident+=1;p=json.dumps({'id':self.ident,'method':method,'params':params or {}}).encode();mask=os.urandom(4);n=len(p)
  header=bytes([129,128|n]) if n<126 else bytes([129,254])+struct.pack('!H',n) if n<65536 else bytes([129,255])+struct.pack('!Q',n)
  self.sock.sendall(header+mask+bytes(x^mask[i%4] for i,x in enumerate(p)))
  while True:
   _,b=self.read(2);n=b&127
   if n==126:n=struct.unpack('!H',self.read(2))[0]
   elif n==127:n=struct.unpack('!Q',self.read(8))[0]
   if b&128:mask=self.read(4)
   value=json.loads(self.read(n))
   if value.get('id')==self.ident:
    if 'error' in value:raise RuntimeError(value['error'])
    return value.get('result',{})
 def js(self,expression):
  result=self.call('Runtime.evaluate',{'expression':expression,'returnByValue':True,'awaitPromise':True})
  if 'exceptionDetails' in result:raise RuntimeError(result['exceptionDetails'])
  return result.get('result',{}).get('value')
 def visit(self,url):
  self.call('Page.navigate',{'url':url})
  for _ in range(100):
   if self.js("document.readyState==='complete'"):break
   time.sleep(.05)
  time.sleep(.2)
 def size(self,width,height=1100):self.call('Emulation.setDeviceMetricsOverride',{'width':width,'height':height,'deviceScaleFactor':1,'mobile':width<768})
 def screenshot(self,path):Path(path).write_bytes(base64.b64decode(self.call('Page.captureScreenshot',{'format':'png'})['data']))
 def __exit__(self,*args):
  if self.sock:self.sock.close()
  self.process.terminate()
  try:self.process.wait(timeout=5)
  except subprocess.TimeoutExpired:self.process.kill()
  self.folder.cleanup()
