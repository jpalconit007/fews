import mysql.connector
from datetime import datetime

# First verify serial import works
try:
    import serial
    print("PySerial version:", serial.__version__)
    print("Serial attributes:", dir(serial))  # Should show 'Serial'
except Exception as e:
    print("Serial import error:", e)
    exit(1)

def insert_into_mysql(water_level_data):
    try:
        connection = mysql.connector.connect(
            host='localhost',
            user='root',
            password='',
            database='fews'
        )
        
        cursor = connection.cursor()
        current_time = datetime.now()
        
        query = """INSERT INTO sensor_readings 
                   (water_level, reading_time) 
                   VALUES (%s, %s)"""
        
        cursor.execute(query, (water_level_data, current_time))
        connection.commit()
        print("Data inserted successfully")
        
    except Exception as e:
        print(f"Database error: {e}")
    finally:
        if 'connection' in locals() and connection.is_connected():
            cursor.close()
            connection.close()

try:
    ser = serial.Serial('COM3', 115200, timeout=1)
    print(f"Serial port {ser.name} opened successfully")
    
    try:
        print("Listening for serial data...")
        while True:
            if ser.in_waiting > 0:
                data = ser.readline().decode('utf-8').strip()
                print(f"{data}")
                insert_into_mysql(data)
                
    except KeyboardInterrupt:
        print("\nProgram terminated by user")
        
finally:
    if 'ser' in locals():
        ser.close()
        print("Serial port closed")