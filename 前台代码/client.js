 var  wsServer = 'ws://210.73.27.35:9501';
 try {
     websocket = new WebSocket(wsServer);
 }
 catch (ex){
     alert('浏览器不支持 WebSocket');
 }
 websocket.onopen = function (evt) { onOpen(evt) }; 
 websocket.onclose = function (evt) { onClose(evt) }; 
 websocket.onmessage = function (evt) { onMessage(evt) }; 
 websocket.onerror = function (evt) { onError(evt) }; 
 function onOpen(evt) { 
    console.log("Connected to WebSocket server."); 
 } 
 function onClose(evt) { 
    console.log("Disconnected"); 
 }
 function onError(evt) { 
    console.log('Error occured: ' + evt.data); 
 }
