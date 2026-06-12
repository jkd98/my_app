Personal PHP web app to practice hexagonal architecture + DDD.

**Features planned:**
- Login / authentication
- Training routine manager
- Personal finance tracker
- Password manager (sensitive!)

**Why:** Learning exercise. Priorities: architecture correctness first, then OWASP security.

**How to apply:** Treat every feature as a teaching opportunity. Push the user to identify bounded contexts, ports, adapters, and domain entities themselves before confirming.

---

## Progreso actual (2026-06-12)

### Bounded Context: Auth — Domain COMPLETO ✓

```
src/
  Shared/Domain/ValueObject/UUIDv7.php                              ✓
  Shared/Domain/Event/DomainEventInterface.php                      ✓
  Auth/Domain/ValueObject/UserId.php                                ✓
  Auth/Domain/ValueObject/Email.php                                 ✓
  Auth/Domain/ValueObject/RawPassword.php                           ✓ (fromString() para login sin validar longitud)
  Auth/Domain/ValueObject/Password.php                              ✓
  Auth/Domain/ValueObject/UserName.php                              ✓
  Auth/Domain/ValueObject/LastName.php                              ✓
  Auth/Domain/ValueObject/TokenId.php                               ✓ (método: generate())
  Auth/Domain/ValueObject/TokenValue.php                            ✓
  Auth/Domain/ValueObject/TokenExpiration.php                       ✓ (30 min)
  Auth/Domain/ValueObject/TokenType.php (enum)                      ✓
  Auth/Domain/ValueObject/RefreshTokenExpiration.php                ✓ (7 días, con fromString() para DB)
  Auth/Domain/Entity/User.php                                       ✓
  Auth/Domain/Entity/VerificationToken.php                          ✓
  Auth/Domain/Entity/RefreshToken.php                               ✓ (con userAgent)
  Auth/Domain/Repository/UserRepositoryInterface.php                ✓
  Auth/Domain/Repository/VerificationTokenRepositoryInterface.php   ✓
  Auth/Domain/Repository/RefreshTokenRepositoryInterface.php        ✓ (save, findByTokenValue, delete, deleteAll)
  Auth/Domain/Service/PasswordHashInterface.php                     ✓
  Auth/Domain/Service/VerifyEmailExist.php                          ✓
  Auth/Domain/Events/UserRegistered.php                             ✓
  Auth/Domain/Exception/EmailAlreadyExistsException.php             ✓
  Auth/Domain/Exception/InvalidCredentialsException.php             ✓
  Auth/Domain/Exception/InvalidTokenException.php                   ✓

  Shared/Application/EventDispatcherInterface.php                   ✓
  Shared/Application/TransactionManagerInterface.php                ✓
  Auth/Application/UseCase/RegisterUser.php                         ✓
  Auth/Application/UseCase/AccountConfirm.php                       ✓
  Auth/Application/UseCase/Login.php                                ✓ (genera access + refresh token)
  Auth/Application/UseCase/Logout.php                               ✓
  Auth/Application/UseCase/LogoutAll.php                            ✓
  Auth/Application/UseCase/Refresh.php                              ✓ (token rotation: guarda nuevo, borra viejo, dentro de transacción)
  Auth/Application/UseCase/PasswordRecovery.php                     ← SIGUIENTE
  Auth/Application/DTO/RegisterUserRequestDTO.php                   ✓
  Auth/Application/DTO/LoginRequestDTO.php                          ✓ (incluye userAgent)
  Auth/Application/DTO/LoginResponseDTO.php                         ✓ (accessToken + refreshToken)
  Auth/Application/Security/TokenGeneratorInterface.php             ✓
  Auth/Infrastructure/Persistence/UserRepository.php                pendiente
  Auth/Infrastructure/Http/Controllers/                             pendiente
  Auth/Infrastructure/Security/JWTGenerate.php                      pendiente
  Auth/Infrastructure/Security/PasswordHash.php                     pendiente
  Auth/Infrastructure/EventListener/SendEmailConfirmation.php       pendiente
  Shared/Application/Port/MailerInterface.php                       pendiente
  Shared/Infrastructure/Mailer/SmtpMailer.php                       pendiente
```

### Decisiones de diseño tomadas
- `PasswordHashInterface` en `Auth/Domain/Service/` (invariante de dominio)
- `TokenGeneratorInterface` en `Auth/Application/Security/` (decisión técnica — JWT es infraestructura)
- `RawPassword::create()` valida longitud mínima 8 chars (registro). `fromString()` sin validación (login — no revelar política de contraseñas)
- `Password` VO almacena hash, sin equals() — verificación via `PasswordHashInterface::verify()`
- `VerificationToken::create()` genera TokenId/TokenValue/TokenExpiration internamente
- `RefreshToken` es entidad separada de `VerificationToken` (ciclos de vida diferentes)
- `RefreshToken` almacena `userAgent` para identificar dispositivo
- `User::register()` recibe `UserId` desde el Use Case (idempotencia)
- DTOs: uno por caso de uso, sin DTO cuando el Use Case recibe un solo parámetro
- Domain Event Recording: `User` tiene `$domainEvents[]` + `pullDomainEvents()`
- Timestamps en User: `DateTimeImmutable` primitivo, no VO
- Transacciones via `TransactionManagerInterface` port en Shared/Application
- Eventos se despachan DESPUÉS del commit (fuera de la transacción)
- El repositorio reconstituye entidades — el Use Case nunca llama a `reconstitute()` directamente
- `TokenExpiration::isExpired()` retorna bool (CQS), el caller lanza la excepción
- JWT payload contiene solo `UserId` (OWASP — mínimo PII)
- Token rotation implementado: al renovar, se elimina el refresh token viejo y se genera uno nuevo
- Login: mensaje específico "cuenta no confirmada" (UX > OWASP estricto en este caso)
- `InvalidTokenException` en `Auth/Domain/Exception/` (es regla de dominio, no de aplicación)
- `DomainEventInterface` en `Shared/Domain/Event/` (no es un port — no conecta con infraestructura)
- `UserRegistered` evento: eventId (string) y occurredAt (DateTimeImmutable) generados internamente; email y userName recibidos como VOs de Auth
