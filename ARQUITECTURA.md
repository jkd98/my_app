# Guía de Arquitectura Hexagonal + DDD

## ¿Por qué interfaces?

Una interfaz es un **contrato**: define *qué* hace algo, sin decir *cómo* lo hace.

Cuando una clase depende de una interfaz en lugar de una clase concreta, ocurren tres cosas buenas:

1. **Puedes cambiar la implementación sin tocar quien la usa.**
   `Login` depende de `PasswordHashInterface`. Si mañana cambias de Argon2id a bcrypt, solo cambias `PasswordHash.php` — `Login` no se entera.

2. **Puedes testear sin infraestructura real.**
   En un test, puedes pasar un `FakeMailer` que implementa `MailerInterface` sin necesitar un servidor SMTP real.

3. **Las dependencias apuntan hacia adentro.**
   El dominio no sabe que existe MySQL ni PHPMailer. Solo sabe que existe algo que guarda usuarios y algo que envía emails. Esto es la regla de oro de la arquitectura hexagonal.

---

## Las tres capas

```
┌─────────────────────────────────────┐
│           INFRAESTRUCTURA           │  ← Detalles técnicos concretos
│  MySQL, PHPMailer, JWT, HTTP...     │
├─────────────────────────────────────┤
│            APLICACIÓN               │  ← Orquesta el flujo
│  Use Cases, DTOs, Ports            │
├─────────────────────────────────────┤
│              DOMINIO                │  ← Reglas de negocio puras
│  Entities, Value Objects, Events   │
└─────────────────────────────────────┘
```

Las dependencias siempre apuntan **hacia adentro**:
- Infraestructura conoce a Aplicación y Dominio.
- Aplicación conoce al Dominio.
- El Dominio **no conoce a nadie**.

---

## Capa de Dominio

**Contiene:** Entities, Value Objects, Domain Events, Repository Interfaces, Domain Services, Exceptions.

**Regla:** No tiene dependencias externas. Cero. No importa PDO, no importa PHPMailer, no importa nada de PHP que no sea el lenguaje puro.

**¿Qué va aquí?** Todo lo que un experto de negocio (no técnico) entendería:
- "Un usuario tiene email y contraseña" → `User`, `Email`, `Password`
- "Las contraseñas nunca se guardan en texto plano" → `PasswordHashInterface` (es una regla de negocio, no técnica)
- "Un token expira en 30 minutos" → `TokenExpiration`
- "El email debe ser único" → `EmailAlreadyExistsException`

**¿Por qué `PasswordHashInterface` está en Dominio y no en Aplicación?**
Porque "las contraseñas nunca se guardan en texto plano" es una **invariante de negocio**. Si alguien crea un `User` sin hashear la contraseña, el dominio está violado. El dominio debe protegerse a sí mismo.

---

## Capa de Aplicación

**Contiene:** Use Cases, DTOs de entrada/salida, Ports (interfaces técnicas).

**Regla:** Orquesta. No contiene lógica de negocio — la delega al dominio. No sabe nada de HTTP, MySQL, ni SMTP.

**¿Qué hace un Use Case?**
Coordina el flujo: obtiene datos, llama al dominio, persiste, despacha eventos. Es el director de orquesta.

**¿Qué son los Ports aquí?**
Son interfaces que el Use Case necesita pero cuya implementación pertenece a infraestructura:
- `TransactionManagerInterface` → el Use Case necesita transacciones, pero no sabe si es MySQL o PostgreSQL.
- `MailerInterface` → el Use Case (a través del listener) necesita enviar emails, pero no sabe si es SMTP o SendGrid.
- `EventDispatcherInterface` → el Use Case notifica que algo ocurrió, pero no sabe quién escucha.

**¿Por qué estos ports NO están en Dominio?**
Porque son decisiones técnicas. Un experto de negocio no sabe qué es SMTP ni una transacción de base de datos. Son conceptos de infraestructura que la aplicación necesita coordinar.

---

## Capa de Infraestructura

**Contiene:** Implementaciones concretas (Adapters), Controllers HTTP, Mailers, Repositorios con PDO.

**Regla:** Aquí viven todos los detalles técnicos. Implementa los Ports que definieron Dominio y Aplicación.

**Los Adapters son las implementaciones de los Ports:**

| Port (interfaz)                    | Adapter (implementación)         |
|------------------------------------|----------------------------------|
| `UserRepositoryInterface`          | `UserRepository` (PDO + MySQL)   |
| `PasswordHashInterface`            | `PasswordHash` (Argon2id)        |
| `TokenGeneratorInterface`          | `JWTGenerate` (RS256)            |
| `MailerInterface`                  | `SmtpMailer` (PHPMailer)         |
| `TransactionManagerInterface`      | Implementación PDO               |
| `EventDispatcherInterface`         | Implementación con listeners     |

Si mañana quieres cambiar MySQL por PostgreSQL, solo reescribes `UserRepository.php`. El resto del proyecto no cambia. Eso es lo que validarás con tu experimento de dos equipos.

---

## El flujo completo: Registro de usuario

```
HTTP Request
    ↓
Controller (Infraestructura)
    ↓ crea RegisterUserRequestDTO
Use Case: RegisterUser (Aplicación)
    ↓ valida email único via VerifyEmailExist
    ↓ hashea contraseña via PasswordHashInterface
    ↓ crea User::register() → graba UserRegistered en $domainEvents
    ↓ crea VerificationToken::create()
    ↓ TRANSACCIÓN: guarda User + Token
    ↓ pullDomainEvents() → despacha UserRegistered
EventDispatcher (Infraestructura)
    ↓ busca listeners de UserRegistered
SendEmailConfirmation (Infraestructura)
    ↓ obtiene token del repositorio
    ↓ construye MailDTO
SmtpMailer (Infraestructura)
    ↓ envía email real
```

El Use Case nunca supo que existía PHPMailer. El dominio nunca supo que existía MySQL. Cada capa solo conoce la abstracción que necesita.

---

## Resumen rápido

| Pregunta                                  | Respuesta                                      |
|-------------------------------------------|------------------------------------------------|
| ¿Es una regla que el negocio entiende?    | → Dominio                                      |
| ¿Es una decisión técnica que el Use Case necesita coordinar? | → Port en Aplicación      |
| ¿Es una implementación concreta?          | → Infraestructura                              |
| ¿Depende de una librería externa?         | → Infraestructura                              |
| ¿El experto de negocio lo entendería?     | → Dominio                                      |
