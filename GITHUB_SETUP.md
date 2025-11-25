# Instrucciones para Subir a GitHub

## ✅ Lo que ya se hizo automáticamente:

1. ✅ Creado `.gitignore`
2. ✅ Inicializado repositorio Git (`git init`)
3. ✅ Agregados todos los archivos (`git add .`)
4. ✅ Creado commit inicial
5. ✅ Configurada rama principal como `main`
6. ✅ Agregado remote: `https://github.com/1n4r1US/inteegradora.git`

## ⚠️ Acción Requerida: Autenticación

El push falló porque GitHub requiere autenticación. Tienes **3 opciones**:

---

### Opción 1: Usar GitHub Desktop (Más Fácil) ⭐

1. Descarga e instala [GitHub Desktop](https://desktop.github.com/)
2. Inicia sesión con tu cuenta de GitHub
3. En GitHub Desktop: **File → Add Local Repository**
4. Selecciona: `c:\wamp64\www\integradora-backend`
5. Haz clic en **"Publish repository"**
6. ✅ ¡Listo!

---

### Opción 2: Usar Personal Access Token (Recomendado)

1. **Generar Token**:
   - Ve a: https://github.com/settings/tokens
   - Clic en **"Generate new token (classic)"**
   - Nombre: `integradora-backend`
   - Permisos: Marca **`repo`** (todos los sub-permisos)
   - Clic en **"Generate token"**
   - **COPIA EL TOKEN** (solo se muestra una vez)

2. **Hacer Push**:
   ```bash
   cd c:\wamp64\www\integradora-backend
   git push -u origin main
   ```
   
   Cuando pida credenciales:
   - **Username**: `1n4r1US`
   - **Password**: `[PEGA TU TOKEN AQUÍ]`

---

### Opción 3: Usar SSH (Para usuarios avanzados)

1. **Generar clave SSH** (si no tienes una):
   ```bash
   ssh-keygen -t ed25519 -C "tu-email@ejemplo.com"
   ```

2. **Agregar clave a GitHub**:
   - Copia el contenido de: `C:\Users\Daniel\.ssh\id_ed25519.pub`
   - Ve a: https://github.com/settings/keys
   - Clic en **"New SSH key"**
   - Pega la clave y guarda

3. **Cambiar remote a SSH**:
   ```bash
   cd c:\wamp64\www\integradora-backend
   git remote set-url origin git@github.com:1n4r1US/inteegradora.git
   git push -u origin main
   ```

---

## 🔍 Verificar que Funcionó

Después de hacer push, visita:
```
https://github.com/1n4r1US/inteegradora
```

Deberías ver todos tus archivos, incluyendo:
- ✅ README.md
- ✅ TECHNICAL_DOCS.md
- ✅ backend/
- ✅ js/
- ✅ assets/

---

## 📝 Comandos Git Útiles para el Futuro

```bash
# Ver estado
git status

# Agregar cambios
git add .

# Hacer commit
git commit -m "Descripción de cambios"

# Subir cambios
git push

# Ver historial
git log --oneline

# Ver remote configurado
git remote -v
```

---

## ❓ Solución de Problemas

### Error: "repository not found"
- Verifica que el repositorio exista en: https://github.com/1n4r1US/inteegradora
- Si no existe, créalo primero en GitHub

### Error: "authentication failed"
- Usa un Personal Access Token en lugar de tu contraseña
- O usa GitHub Desktop

### Error: "rejected - non-fast-forward"
- El repositorio remoto tiene cambios que no tienes localmente
- Solución: `git pull origin main --rebase` y luego `git push`

---

**Recomendación**: Si es tu primera vez con Git, usa **GitHub Desktop** (Opción 1). Es la forma más sencilla.
