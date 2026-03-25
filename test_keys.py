import urllib.request
import json

url = "https://mungbffwwjobinfcympd.supabase.co/rest/v1/configuracion?id=eq.1"

key1 = "EyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im11bmdiZmZ3d2pvYmluZmN5bXBkIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzQ0NDIzNzcsImV4cCI6MjA5MDAxODM3N30.nkMo-QRJfZ1Z1gzuaZNvnw2kgChc_BGVHqpy0uPTVyY"
key2 = "sb_publishable_yW5gsHp1w8ElbWRTeoMJYw_ZC-u0j9e"

def test_key(key, name):
    req = urllib.request.Request(url, headers={
        "apikey": key,
        "Authorization": f"Bearer {key}"
    })
    try:
        with urllib.request.urlopen(req) as response:
            print(f"[{name}] Success! Status: {response.status}")
            return True
    except urllib.error.HTTPError as e:
        print(f"[{name}] Failed: {e.code} - {e.reason}")
        return False

print("Testing old JWT key...")
test_key(key1, "Key 1 (JWT)")

print("Testing new publishable key...")
test_key(key2, "Key 2 (sb_publishable)")
