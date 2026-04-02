-- ==============================================================================
-- INSTRUCCIONES SQL PARA EL SOPORTE MULTI-PROYECTO EN RINCÓN DE BAVIERA
-- ==============================================================================
-- Ejecuta esto en el SQL Editor de tu proyecto en Supabase (https://app.supabase.com)
-- ==============================================================================

-- 1. Crear la tabla principal de proyectos
CREATE TABLE IF NOT EXISTS public.proyectos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nombre TEXT NOT NULL,
    descripcion TEXT,
    imagen_portada TEXT,
    estado TEXT DEFAULT 'activo', -- Puede ser 'activo', 'inactivo', 'oculto'
    created_at TIMESTAMP WITH TIME ZONE DEFAULT timezone('utc'::text, now()) NOT NULL
);

ALTER TABLE public.proyectos ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Permitir select en proyectos" ON public.proyectos FOR SELECT USING (true);
CREATE POLICY "Permitir insert en proyectos" ON public.proyectos FOR INSERT WITH CHECK (true);
CREATE POLICY "Permitir update en proyectos" ON public.proyectos FOR UPDATE USING (true);
CREATE POLICY "Permitir delete en proyectos" ON public.proyectos FOR DELETE USING (true);

-- ==============================================================================
-- 2. Modificar tablas existentes para relacionarlas con un proyecto
-- (Añadiremos la columna proyecto_id UUID REFERENCES proyectos(id) ON DELETE CASCADE)
-- ==============================================================================

-- CONFIGURACION
-- (Opcionalmente puedes migrar el id=1 a un proyecto, pero por simplicidad permitimos que tenga proyecto_id)
ALTER TABLE public.configuracion ADD COLUMN IF NOT EXISTS proyecto_id UUID REFERENCES public.proyectos(id) ON DELETE CASCADE;

-- LOTES
ALTER TABLE public.lotes ADD COLUMN IF NOT EXISTS proyecto_id UUID REFERENCES public.proyectos(id) ON DELETE CASCADE;

-- POIS
ALTER TABLE public.pois ADD COLUMN IF NOT EXISTS proyecto_id UUID REFERENCES public.proyectos(id) ON DELETE CASCADE;

-- POLIGONOS_360
ALTER TABLE public.poligonos_360 ADD COLUMN IF NOT EXISTS proyecto_id UUID REFERENCES public.proyectos(id) ON DELETE CASCADE;

-- INTERESADOS
ALTER TABLE public.interesados ADD COLUMN IF NOT EXISTS proyecto_id UUID REFERENCES public.proyectos(id) ON DELETE CASCADE;

-- ==============================================================================
-- 3. Crear Políticas (RLS) para que sigan siendo accesibles públicamente
--    (si no estaban creadas para las otras tablas, esto asegura que el proyecto funcione)
-- ==============================================================================

-- NOTA: Como es una aplicación frontal, permitimos lectura/escritura pública por simplicidad,
-- para un entorno real de producción se recomienda usar Auth de Supabase para los inserts/updates del Admin.

-- Para configuracion
ALTER TABLE public.configuracion ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "Permitir select en configuracion" ON public.configuracion;
CREATE POLICY "Permitir select en configuracion" ON public.configuracion FOR SELECT USING (true);
DROP POLICY IF EXISTS "Permitir all en configuracion" ON public.configuracion;
CREATE POLICY "Permitir all en configuracion" ON public.configuracion FOR ALL USING (true) WITH CHECK (true);

-- Para lotes
ALTER TABLE public.lotes ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "Permitir select en lotes" ON public.lotes;
CREATE POLICY "Permitir select en lotes" ON public.lotes FOR SELECT USING (true);
DROP POLICY IF EXISTS "Permitir all en lotes" ON public.lotes;
CREATE POLICY "Permitir all en lotes" ON public.lotes FOR ALL USING (true) WITH CHECK (true);

-- Para pois
ALTER TABLE public.pois ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "Permitir select en pois" ON public.pois;
CREATE POLICY "Permitir select en pois" ON public.pois FOR SELECT USING (true);
DROP POLICY IF EXISTS "Permitir all en pois" ON public.pois;
CREATE POLICY "Permitir all en pois" ON public.pois FOR ALL USING (true) WITH CHECK (true);

-- Para interesados
ALTER TABLE public.interesados ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS "Permitir select en interesados" ON public.interesados;
CREATE POLICY "Permitir select en interesados" ON public.interesados FOR SELECT USING (true);
DROP POLICY IF EXISTS "Permitir insert en interesados" ON public.interesados;
CREATE POLICY "Permitir insert en interesados" ON public.interesados FOR INSERT WITH CHECK (true);
DROP POLICY IF EXISTS "Permitir update en interesados" ON public.interesados;
CREATE POLICY "Permitir update en interesados" ON public.interesados FOR UPDATE USING (true);
DROP POLICY IF EXISTS "Permitir delete en interesados" ON public.interesados;
CREATE POLICY "Permitir delete en interesados" ON public.interesados FOR DELETE USING (true);

-- ==============================================================================
-- SI AL HACER EL ALTER TABLE TE DA ERROR PORQUE LAS TABLAS NO EXISTEN:
-- Es porque debes crearlas primero. Aquí tienes las definiciones por si te faltaba alguna:
-- ==============================================================================

CREATE TABLE IF NOT EXISTS public.configuracion (
    id TEXT PRIMARY KEY DEFAULT gen_random_uuid()::text,
    titulo_texto TEXT,
    imagen_url TEXT,
    cuota_inicial TEXT,
    asesor_tel TEXT,
    proyecto_id UUID REFERENCES public.proyectos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS public.lotes (
    id TEXT PRIMARY KEY,
    nombre TEXT,
    area TEXT,
    precio TEXT,
    estado TEXT,
    yaw REAL,
    pitch REAL,
    poligono TEXT,
    proyecto_id UUID REFERENCES public.proyectos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS public.pois (
    id TEXT PRIMARY KEY,
    content TEXT,
    yaw REAL,
    pitch REAL,
    proyecto_id UUID REFERENCES public.proyectos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS public.interesados (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nombre TEXT NOT NULL,
    telefono TEXT,
    email TEXT,
    mensaje TEXT,
    lote_id TEXT,
    valor_inmueble NUMERIC,
    cuota_inicial NUMERIC,
    plazo_meses INTEGER,
    fecha_registro TIMESTAMP WITH TIME ZONE DEFAULT timezone('utc'::text, now()) NOT NULL,
    proyecto_id UUID REFERENCES public.proyectos(id) ON DELETE CASCADE
);
