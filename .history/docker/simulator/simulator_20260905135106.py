import json
import os
import random
import time

import paho.mqtt.client as mqtt


MQTT_HOST = os.getenv("MQTT_HOST", "mosquitto")
MQTT_PORT = int(os.getenv("MQTT_PORT", "1883"))

client = mqtt.Client(
    mqtt.CallbackAPIVersion.VERSION2,
    client_id="sensor-simulator"
)

print(f"Connecting to MQTT broker {MQTT_HOST}:{MQTT_PORT}")

client.connect(MQTT_HOST, MQTT_PORT)

client.loop_start()

try:
    while True:

        temperature = round(random.uniform(18.0, 30.0), 2)
        humidity = round(random.uniform(35.0, 70.0), 2)

        message = {
            "device_id": "sensor-001",
            "temperature": temperature,
            "humidity": humidity,
            "timestamp": int(time.time())
        }

        payload = json.dumps(message)

        print(f"Publishing: {payload}")

        client.publish(
            "iot/device/sensor-001/measurement",
            payload
        )

        time.sleep(5)

except KeyboardInterrupt:
    pass

finally:
    client.loop_stop()
    client.disconnect()