 var  wsServer = 'ws://localhost:8888/Demo'; 
 var  websocket = new WebSocket(wsServer); 
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
 function onMessage(evt) { 
    console.log(evt.data); 
 } 
 function onError(evt) { 
    console.log('Error occured: ' + evt.data); 
 }
