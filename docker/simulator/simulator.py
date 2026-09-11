import json
import os
import random
import time

import paho.mqtt.client as mqtt
import pymysql


MQTT_HOST = os.getenv("MQTT_HOST", "mosquitto")
MQTT_PORT = int(os.getenv("MQTT_PORT", "1883"))

DB_HOST = os.getenv("DB_HOST", "mariadb")
DB_PORT = int(os.getenv("DB_PORT", "3306"))
DB_NAME = os.getenv("DB_DATABASE", "iot")
DB_USER = os.getenv("DB_USERNAME", "iot")
DB_PASSWORD = os.getenv("DB_PASSWORD", "iot_password")

SENSOR_REFRESH_SECONDS = int(os.getenv("SENSOR_REFRESH_SECONDS", "30"))
PUBLISH_INTERVAL_SECONDS = int(os.getenv("PUBLISH_INTERVAL_SECONDS", "5"))


def wait_for_mqtt() -> mqtt.Client:
    client = mqtt.Client(
        mqtt.CallbackAPIVersion.VERSION2,
        client_id="sensor-simulator",
    )

    while True:
        try:
            print(f"Connecting to MQTT broker {MQTT_HOST}:{MQTT_PORT}")
            client.connect(MQTT_HOST, MQTT_PORT)
            client.loop_start()
            return client
        except Exception as error:
            print(f"MQTT not ready ({error}), retrying in 2s")
            time.sleep(2)


def fetch_device_ids() -> list[str]:
    connection = pymysql.connect(
        host=DB_HOST,
        port=DB_PORT,
        user=DB_USER,
        password=DB_PASSWORD,
        database=DB_NAME,
        cursorclass=pymysql.cursors.DictCursor,
    )

    try:
        with connection.cursor() as cursor:
            cursor.execute("SELECT device_id FROM sensor ORDER BY id")
            rows = cursor.fetchall()
    finally:
        connection.close()

    return [row["device_id"] for row in rows if row.get("device_id")]


def load_device_ids() -> list[str]:
    while True:
        try:
            device_ids = fetch_device_ids()
            if device_ids:
                print(f"Loaded device ids from sensor table: {device_ids}")
                return device_ids

            print("No sensors found in iot.sensor, retrying in 5s")
        except Exception as error:
            print(f"Database not ready ({error}), retrying in 5s")

        time.sleep(5)


client = wait_for_mqtt()
device_ids = load_device_ids()
last_refresh = time.monotonic()

try:
    while True:
        now = time.monotonic()
        if now - last_refresh >= SENSOR_REFRESH_SECONDS:
            device_ids = load_device_ids()
            last_refresh = now

        for device_id in device_ids:
            temperature = round(random.uniform(18.0, 30.0), 2)
            humidity = round(random.uniform(35.0, 70.0), 2)

            message = {
                "device_id": device_id,
                "temperature": temperature,
                "humidity": humidity,
                "timestamp": int(time.time()),
            }

            payload = json.dumps(message)
            topic = f"iot/device/{device_id}/measurement"

            print(f"Publishing to {topic}: {payload}")
            client.publish(topic, payload)

        time.sleep(PUBLISH_INTERVAL_SECONDS)

except KeyboardInterrupt:
    pass

finally:
    client.loop_stop()
    client.disconnect()
