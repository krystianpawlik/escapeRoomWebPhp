# import requests


# dataToSend = {"action":"power_connector", "value" : "alive"}
# response = requests.post('http://localhost:8000/database_api.php', data = dataToSend)

# print(response)
# print("end")


import requests

url = "http://localhost:8000/database_api.php"  # <-- zamień na prawdziwy adres
payload = {
    "device": "power_connector",
    "value": "power_ok"
}

headers = {
    "Content-Type": "application/json"
}

response = requests.post(url, json=payload, headers=headers)

print("Status code:", response.status_code)
print("Response:", response.text)