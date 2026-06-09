---
name: project-context
description: Goals and current state of the personal web app project
metadata: 
  node_type: memory
  type: project
  originSessionId: 89365430-8857-4817-a289-9c4a6b4d58f7
---

Personal PHP web app to practice hexagonal architecture + DDD.

**Features planned:**
- Login / authentication
- Training routine manager
- Personal finance tracker
- Password manager (sensitive!)

**Why:** Learning exercise. Priorities: architecture correctness first, then OWASP security.

**How to apply:** Treat every feature as a teaching opportunity. Push the user to identify bounded contexts, ports, adapters, and domain entities themselves before confirming.

---

## Progreso actual (2026-06-09)

### Bounded Context: Auth

**Estructura de directorios diseñada y aprobada:**
```
src/
  Shared/Domain/ValueObject/UUIDv7.php          ✓ COMPLETO
  Auth/Domain/ValueObject/UserId.php             ✓ COMPLETO
  Auth/Domain/ValueObject/Email.php              ✓ COMPLETO
  Auth/Domain/ValueObject/RawPassword.php        ✓ COMPLETO
  Auth/Domain/ValueObject/Password.php           ✓ COMPLETO
  Auth/Domain/ValueObject/UserName.php           ✓ COMPLETO
  Auth/Domain/ValueObject/LastName.php           ✓ COMPLETO
  Auth/Domain/Entity/User.php                    ← EN PROGRESO
  Auth/Domain/Entity/VerificationToken.php       pendiente
  Auth/Domain/Repository/UserRepositoryInterface.php  pendiente
  Auth/Domain/Service/VerifyEmailExist.php       pendiente
  Auth/Domain/Service/PasswordHashInterface.php  pendiente
  Auth/Domain/Events/UserRegistered.php          pendiente
  Auth/Application/UseCase/RegisterUser.php      pendiente
  Auth/Application/UseCase/AccountConfirm.php    pendiente
  Auth/Application/UseCase/Login.php             pendiente
  Auth/Application/UseCase/Logout.php            pendiente
  Auth/Application/UseCase/PasswordRecovery.php  pendiente
  Auth/Application/DTO/ (uno por caso de uso)    pendiente
  Auth/Application/Security/TokenGeneratorInterface.php  pendiente
  Auth/Infrastructure/Persistence/UserRepository.php     pendiente
  Auth/Infrastructure/Http/Controllers/           pendiente
  Auth/Infrastructure/Security/JWTGenerate.php   pendiente
  Auth/Infrastructure/Security/PasswordHash.php  pendiente
  Auth/Infrastructure/EventListener/SendEmailConfirmation.php  pendiente
  Shared/Application/Port/MailerInterface.php    pendiente
  Shared/Infrastructure/Mailer/SmtpMailer.php    pendiente
```

**Decisiones de diseño tomadas:**
- `UUIDv7` usa `ramsey/uuid` instalado via composer
- `PasswordHashInterface` vive en `Auth/Domain/Service/` (invariante de dominio)
- `TokenGeneratorInterface` vive en `Auth/Application/Security/` (decisión técnica)
- `RawPassword` VO valida longitud mínima 8 chars (OWASP), no se persiste
- `Password` VO almacena el hash, sin equals() — verificación via PasswordHasherInterface::verify()
- `VerificationToken` es una Entity dentro de Auth (no un Bounded Context separado), cubre confirmación de cuenta Y recuperación de contraseña
- DTOs: uno por caso de uso (no genéricos)
- `User` entity: constructor privado, factory `register()` dispara UserRegistered event, factory `reconstitute()` para reconstruir desde DB, usa Domain Event Recording (array $domainEvents + pullDomainEvents())
- Timestamps en User: `DateTimeImmutable` primitivo, no VO
- `isVerified`: bool simple en User entity
