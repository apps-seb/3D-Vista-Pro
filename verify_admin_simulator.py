from playwright.sync_api import sync_playwright
import time

def test_admin_simulator(page):
    # Abrir admin.html directamente desde archivo
    page.goto(f"file:///app/admin.html")

    # Esperar a que se quite la pantalla de carga (esperar unos segundos)
    time.sleep(3)

    # Hacer click en la pestaña de configuración general para que aparezca el simulador
    page.evaluate("activarModo('general')")

    # Esperar a que el contenedor del simulador sea visible
    page.wait_for_selector("#simulador-valor-lote")

    # Tomar captura de pantalla
    page.screenshot(path="/home/jules/verification/admin_simulator_fields.png")

if __name__ == "__main__":
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context()
        page = context.new_page()
        try:
            test_admin_simulator(page)
            print("Captura tomada: admin_simulator_fields.png")
        except Exception as e:
            print(f"Error: {e}")
        finally:
            browser.close()
