from datetime import datetime 
import socket 
import mysql.connector 
from mysql.connector import Error 
from mysql.connector import errorcode 
 
UDP_IP = "192.168.67.50" 
UDP_PORT = 11000 
 
sock = socket.socket(socket.AF_INET, # Internet 
                     socket.SOCK_DGRAM) # UDP 
sock.bind((UDP_IP, UDP_PORT)) 
connection = mysql.connector.connect(host='localhost', database='db_suhu', user='root', password='') 
while True: 
    data, addr = sock.recvfrom(1024) # buffer size sebesar 1024 bytes 
    print ("Data dari IoT :", data) 
    dummyString = data.decode("utf-8") 
    dataArray = dummyString.split(';') 
    idperangkat=dataArray[0] 
    suhu=dataArray[1] 
    print("ID Perangkat: ", idperangkat) 
    print("Suhu: ", suhu) 
    now = datetime.now() 
    formatted_date = now.strftime('%Y-%m-%d %H:%M:%S') 
    cursor = connection.cursor() 
    sql ="INSERT INTO tbl_temperatur VALUES (%s,%s,%s,%s)" 
    #print(sql) 
    val=(0, idperangkat, suhu, formatted_date) 
    cursor.execute(sql,val) 
    connection.commit() 