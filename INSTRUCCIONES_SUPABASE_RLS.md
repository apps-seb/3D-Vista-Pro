# Solución Definitiva: Error de "Row Level Security (RLS)" en Supabase

El error **"new row violates row-level security policy"** (o similar) sucede porque, por defecto, Supabase bloquea todas las operaciones de lectura, inserción, actualización y borrado cuando la seguridad a nivel de fila (RLS) está activada, a menos que se hayan creado políticas que explícitamente permitan esas acciones.

Dado que tu aplicación (HTML/JS) se conecta directamente a Supabase de manera pública (sin un backend intermedio ni autenticación estricta por usuario), la solución más robusta y rápida es configurar las políticas para permitir el acceso público (anónimo) a las tablas y al bucket de almacenamiento.

Sigue este paso a paso detallado para solucionarlo por completo.

## Paso 1: Configurar el Bucket de Almacenamiento (Storage)

Tus imágenes 360 se suben al bucket `imagenes_360`. Este bucket debe ser público para que las imágenes puedan ser leídas en el visor 360, y debe permitir subidas.

**Opción A: Desde el Panel Gráfico (Dashboard) de Supabase**
1. Ve a tu proyecto en Supabase y haz clic en **Storage** en el menú de la izquierda.
2. Si el bucket `imagenes_360` no existe, créalo haciendo clic en **New Bucket**, nómbralo **`imagenes_360`** y **marca la casilla "Public bucket"**.
3. Haz clic en la opción **Policies** (Políticas) en el panel izquierdo de Storage, o en el candado junto al nombre del bucket.
4. En la sección del bucket `imagenes_360`, haz clic en **New Policy** bajo "Insert" (o crear política personalizada).
5. Selecciona **"For full customization"** (Para personalización completa).
6. Nombra la política (ej. "Permitir subida publica").
7. En "Allowed operations", selecciona **INSERT**, **UPDATE**, **SELECT** y **DELETE**.
8. En "Target roles", puedes dejarlo vacío o seleccionar `anon` y `authenticated`.
9. Haz clic en **Review** y luego en **Save policy**.

**Opción B: Mediante consulta SQL**
Si prefieres hacerlo de inmediato con código:
1. Ve a **SQL Editor** en el menú izquierdo de Supabase.
2. Haz clic en **New Query** (Nueva consulta).
3. Pega el siguiente código y presiona **Run**:

```sql
-- Asegurar que el bucket sea público
UPDATE storage.buckets SET public = true WHERE id = 'imagenes_360';

-- Permitir a cualquier usuario subir, ver, actualizar y borrar archivos en el bucket
CREATE POLICY "Acceso Publico a Imagenes 360"
ON storage.objects
FOR ALL
USING ( bucket_id = 'imagenes_360' )
WITH CHECK ( bucket_id = 'imagenes_360' );
```

---

## Paso 2: Configurar las Políticas de las Tablas (Base de Datos)

El error específico probablemente ocurre cuando el código intenta guardar la URL de la imagen en la tabla `configuracion`. Al igual que el Storage, las tablas necesitan políticas.

Según tu código, interactúas con 3 tablas: `configuracion`, `lotes`, y `pois`.

1. Ve a **SQL Editor** en el menú izquierdo de Supabase.
2. Haz clic en **New Query**.
3. Pega y ejecuta (Run) el siguiente bloque de código. Este código habilita RLS en todas tus tablas y crea políticas para permitir el acceso total de forma pública.

```sql
-- Habilitar RLS en las tablas
ALTER TABLE configuracion ENABLE ROW LEVEL SECURITY;
ALTER TABLE lotes ENABLE ROW LEVEL SECURITY;
ALTER TABLE pois ENABLE ROW LEVEL SECURITY;

-- Crear políticas de acceso total (Lectura, Inserción, Actualización y Borrado) para todos

-- Tabla: configuracion
DROP POLICY IF EXISTS "Acceso publico configuracion" ON configuracion;
CREATE POLICY "Acceso publico configuracion" ON configuracion FOR ALL USING (true) WITH CHECK (true);

-- Tabla: lotes
DROP POLICY IF EXISTS "Acceso publico lotes" ON lotes;
CREATE POLICY "Acceso publico lotes" ON lotes FOR ALL USING (true) WITH CHECK (true);

-- Tabla: pois
DROP POLICY IF EXISTS "Acceso publico pois" ON pois;
CREATE POLICY "Acceso publico pois" ON pois FOR ALL USING (true) WITH CHECK (true);
```

---

## Paso 3: Verificar y Probar

1. Una vez ejecutados los pasos anteriores, recarga la página `admin.html`.
2. Haz clic en **"Cambiar Imagen 360"** y selecciona una imagen.
3. Ahora la barra de progreso debería avanzar hasta el 100%, la imagen se cargará en el visor y no te mostrará el error de RLS, guardando la nueva imagen en la base de datos de manera exitosa.

## Nota de Seguridad Profesional
Esta solución permite el **acceso anónimo** para modificar y leer los datos. En una aplicación en producción, esto significa que cualquiera con el enlace a tu página `admin.html` podría modificar tus datos. Como mejor práctica profesional a futuro, deberías implementar autenticación (Supabase Auth) y cambiar el `USING (true)` en el código SQL por `USING (auth.uid() IS NOT NULL)` para que sólo los usuarios que inicien sesión con un correo y contraseña puedan editar.

¡Con estos pasos, tu problema quedará solucionado de raíz de manera inmediata y profesional!
