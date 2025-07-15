import socket 
 
UDP_IP = "192.168.67.50" 
UDP_PORT = 11000 
 
sock = socket.socket(socket.AF_INET, # Internet 
                     socket.SOCK_DGRAM) # UDP 
sock.bind((UDP_IP, UDP_PORT)) 
while True: 
    data, addr = sock.recvfrom(1024) # buffer size sebesar 1024 bytes 
    print ("Data dari IoT :", data) 
    dummyString = data.decode("utf-8") 
    dataArray = dummyString.split(';') 
    idperangkat=dataArray[0] 
    suhu=dataArray[1] 
    print("ID Perangkat: ", idperangkat) 
    print("Suhu: ", suhu)