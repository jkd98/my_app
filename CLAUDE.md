Personal PHP web app to practice hexagonal architecture + DDD.

**Features planned:**
- Login / authentication
- Training routine manager
- Personal finance tracker
- Password manager (sensitive!)

**Why:** Learning exercise. Priorities: architecture correctness first, then OWASP security.

**How to apply:** Treat every feature as a teaching opportunity. Push the user to identify bounded contexts, ports, adapters, and domain entities themselves before confirming.

---

## Progreso actual (2026-06-11)

### Bounded Context: Auth — Domain COMPLETO ✓

```
src/
  Shared/Domain/ValueObject/UUIDv7.php                              ✓
  Shared/Domain/Event/DomainEventInterface.php                      ✓
  Auth/Domain/ValueObject/UserId.php                                ✓
  Auth/Domain/ValueObject/Email.php                                 ✓
  Auth/Domain/ValueObject/RawPassword.php                           ✓
  Auth/Domain/ValueObject/Password.php                              ✓
  Auth/Domain/ValueObject/UserName.php                              ✓
  Auth/Domain/ValueObject/LastName.php                              ✓
  Auth/Domain/ValueObject/TokenId.php                               ✓
  Auth/Domain/ValueObject/TokenValue.php                            ✓
  Auth/Domain/ValueObject/TokenExpiration.php                       ✓
  Auth/Domain/ValueObject/TokenType.php (enum)                      ✓
  Auth/Domain/Entity/User.php                                       ✓
  Auth/Domain/Entity/VerificationToken.php                          ✓
  Auth/Domain/Repository/UserRepositoryInterface.php                ✓
  Auth/Domain/Repository/VerificationTokenRepositoryInterface.php   ✓
  Auth/Domain/Service/PasswordHashInterface.php                     ✓
  Auth/Domain/Service/VerifyEmailExist.php                          ✓
  Auth/Domain/Events/UserRegistered.php                             ✓
  Auth/Domain/Exception/EmailAlreadyExistsException.php             ✓

  Shared/Application/EventDispatcherInterface.php                   ✓
  Shared/Application/TransactionManagerInterface.php                ✓
  Auth/Application/UseCase/RegisterUser.php                         ✓
  Auth/Application/UseCase/AccountConfirm.php                       ✓
  Auth/Application/UseCase/Login.php                                ← SIGUIENTE
  Auth/Application/UseCase/Logout.php                               pendiente
  Auth/Application/UseCase/PasswordRecovery.php                     pendiente
  Auth/Application/DTO/RegisterUserRequestDTO.php                   ✓
  Auth/Application/Security/TokenGeneratorInterface.php             pendiente
  Auth/Infrastructure/Persistence/UserRepository.php                pendiente
  Auth/Infrastructure/Http/Controllers/                             pendiente
  Auth/Infrastructure/Security/JWTGenerate.php                      pendiente
  Auth/Infrastructure/Security/PasswordHash.php                     pendiente
  Auth/Infrastructure/EventListener/SendEmailConfirmation.php       pendiente
  Shared/Application/Port/MailerInterface.php                       pendiente
  Shared/Infrastructure/Mailer/SmtpMailer.php                       pendiente
```

### Decisiones de diseño tomadas
- `UUIDv7` usa `ramsey/uuid` instalado via composer
- `PasswordHashInterface` en `Auth/Domain/Service/` (invariante de dominio)
- `TokenGeneratorInterface` en `Auth/Application/Security/` (decisión técnica)
- `RawPassword` VO valida longitud mínima 8 chars (OWASP), no se persiste
- `Password` VO almacena el hash, sin equals() — verificación via `PasswordHashInterface::verify()`
- `VerificationToken` es una Entity dentro de Auth (no Bounded Context separado), cubre confirmación Y recuperación de contraseña
- `VerificationToken::create()` genera TokenId/TokenValue/TokenExpiration internamente
- `User::register()` recibe `UserId` desde el Use Case (idempotencia)
- DTOs: uno por caso de uso
- Domain Event Recording: `User` tiene `$domainEvents[]` + `pullDomainEvents()`
- Timestamps en User: `DateTimeImmutable` primitivo, no VO
- `isVerified`: bool simple en User entity
- Transacciones via `TransactionManagerInterface` port en Shared/Application
- Eventos se despachan DESPUÉS del commit (fuera de la transacción)
- El repositorio reconstituye entidades — el Use Case nunca llama a `reconstitute()` directamente
- `TokenExpiration::isExpired()` retorna bool (CQS), el caller lanza la excepción
