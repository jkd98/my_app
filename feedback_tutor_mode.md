---
name: feedback-tutor-mode
description: "User wants tutor/guide mode — no code written, only guidance. Also learning systematic thinking."
metadata: 
  node_type: memory
  type: feedback
  originSessionId: 89365430-8857-4817-a289-9c4a6b4d58f7
  updated: 2026-07-01
---

Do NOT write any code. Only guide, explain, and ask questions.

**Why:** User explicitly said "solo quiero que me guies y no escribas nada de código" — they want to learn by doing, not by copying. User also recognized they depend too much on tutorials/AI and wants to develop **systematic thinking skills**.

**How to apply:** 
- In every response, give direction, ask the user to think, explain concepts, point out what to consider — but never produce implementation code or file content.
- When the user is stuck or uncertain, don't jump to solutions. Instead, guide them to: (1) identify edge cases/what could go wrong, (2) trace the code mentally (what value does this variable have?), (3) validate consistency (does this match what I said earlier?)
- Encourage the "write small code, then verify it" pattern instead of big implementations

**Systematic thinking pattern** (validated 2026-07-01):
When user wrote the `promedio()` function without tutorials, it revealed they CAN think logically. The issue isn't capability, it's methodology. Teach them to:
1. Think about edge cases BEFORE coding (what if array is empty?)
2. Trace mentally (0/0 = what exactly?)
3. Validate their solution (does this match my requirements?)
4. Apply the same pattern recursively to complex problems (like the DI Container)

This pattern works because it separates **logical thinking** (which user can do) from **mechanical errors** (which are normal). User recognized they were following tutorials instead of debugging when code breaks.

### Progreso de sesión 2026-07-01 (completo)
- ✓ Refactorizó Container con lógica clara de $targetClass vs $implementation
- ✓ Creó ContainerConfig con método estático create()
- ✓ Implementó EnvironmentLoader para cargar .env (con filtro de comentarios)
- ✓ Creó bootstrap en /public/index.php
- ✓ Solucionó problema de namespace global (\PDO)
- ✓ Identificó arquitectura modular para HTTP Router (por bounded context)
- ✓ Implementó Router principal y AuthRouter (completos y funcionales)
- ✓ Creó 3 migraciones SQL con constraints nombrados y CHARACTER SET utf8mb4
- ✓ Solucionó problema de zona horaria (UTC en toda la app)
- ✓ Solucionó problema de SMTP (comillas en .env para valores con espacios)
- ✓ Flujo HTTP end-to-end funcionando: Request → Router → DI → Controller → DB → Events → Email

**Aprendizajes clave:**
1. El Container es lazy — solo instancia lo que se pide
2. Separación de responsabilidades — Router busca, Bootstrap instancia, Controller ejecuta
3. Zona horaria profesional — trabajar siempre en UTC
4. Variables de entorno — valores con espacios requieren comillas en .env
5. Debugging con logs — SMTPDebug revela problemas reales vs errores genéricos
6. **DDD es transferible al frontend** — usar bounded contexts, módulos, separación clara
7. **Arquitectura antes de código** — pensar estructura antes de escribir línea 1
8. **HTML puro para aprender** — separar frontend (navegador) de backend (servidor)
9. **Reutilización en JS** — igual que en PHP, pero con funciones/módulos JS

### Sesión 2026-07-04 (Frontend setup)
- ✓ Pensó en arquitectura frontend usando DDD (bounded contexts + módulos)
- ✓ Decidió estructura: `frontend/Auth/Register/`, `frontend/shared/`
- ✓ Entendió diferencia: PHP genera HTML (servidor) vs HTML/JS ejecuta (navegador)
- ✓ Eligió vanilla JS + localhost:3000 para aprender fundamentos
- ✓ Identificó que DDD/Hexagonal Architecture aplica a frontend también
- ✓ Escribió formulario HTML con BEM (Block__Element--Modifier)
- ✓ Aprendió mobile-first CSS (base mobile, media queries)
- ✓ Entendió CSS reset y `1rem = 10px`
- ✓ Pensó en diseño antes de escribir código (centrado, responsive)
- **Aprendizaje meta:** El usuario trasladó patrones de backend al frontend sin ser guiado — indica comprensión profunda
- **Patrón:** Pensamiento arquitectónico antes de implementación (igual que en backend)
