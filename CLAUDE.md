Personal PHP web app to practice hexagonal architecture + DDD.

**Features planned:**
- Login / authentication
- Training routine manager
- Personal finance tracker
- Password manager (sensitive!)

**Why:** Learning exercise. Priorities: architecture correctness first, then OWASP security.

**How to apply:** Treat every feature as a teaching opportunity. Push the user to identify bounded contexts, ports, adapters, and domain entities themselves before confirming.

---

## Progreso actual (2026-06-13)

### Bounded Context: Auth — Domain COMPLETO ✓
### Application layer — COMPLETO ✓
### Infrastructure layer — EN PROGRESO

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
  Auth/Domain/ValueObject/TokenExpiration.php                       ✓ (30 min, con fromString() para DB)
  Auth/Domain/ValueObject/TokenType.php (enum)                      ✓
  Auth/Domain/ValueObject/RefreshTokenExpiration.php                ✓ (7 días, con fromString() para DB)
  Auth/Domain/Entity/User.php                                       ✓
  Auth/Domain/Entity/VerificationToken.php                          ✓
  Auth/Domain/Entity/RefreshToken.php                               ✓ (con userAgent)
  Auth/Domain/Repository/UserRepositoryInterface.php                ✓
  Auth/Domain/Repository/VerificationTokenRepositoryInterface.php   ✓ (+ deleteAllByTokenType, findByTokenTypeAndUserId)
  Auth/Domain/Repository/RefreshTokenRepositoryInterface.php        ✓ (save, findByTokenValue, delete, deleteAll)
  Auth/Domain/Service/PasswordHashInterface.php                     ✓ (+ needsRehash)
  Auth/Domain/Service/VerifyEmailExist.php                          ✓
  Auth/Domain/Events/UserRegistered.php                             ✓
  Auth/Domain/Events/PasswordRecoveryRequested.php                  ✓
  Auth/Domain/Exception/EmailAlreadyExistsException.php             ✓
  Auth/Domain/Exception/InvalidCredentialsException.php             ✓
  Auth/Domain/Exception/InvalidTokenException.php                   ✓

  Shared/Application/Port/EventDispatcherInterface.php              ✓
  Shared/Application/Port/TransactionManagerInterface.php           ✓
  Shared/Application/Port/MailerInterface.php                       ✓ (send(MailDTO): void)
  Shared/Application/DTO/MailDTO.php                                ✓ (recipients array, subject, messageBody)
  Auth/Application/UseCase/RegisterUser.php                         ✓
  Auth/Application/UseCase/AccountConfirm.php                       ✓
  Auth/Application/UseCase/Login.php                                ✓ (genera access + refresh token, needsRehash)
  Auth/Application/UseCase/Logout.php                               ✓
  Auth/Application/UseCase/LogoutAll.php                            ✓
  Auth/Application/UseCase/Refresh.php                              ✓ (token rotation)
  Auth/Application/UseCase/PasswordRecovery.php                     ✓ (Fase 1 — solicitar recuperación)
  Auth/Application/UseCase/ResetPassword.php                        ✓ (Fase 2 — cambiar contraseña)
  Auth/Application/DTO/RegisterUserRequestDTO.php                   ✓
  Auth/Application/DTO/LoginRequestDTO.php                          ✓ (incluye userAgent)
  Auth/Application/DTO/LoginResponseDTO.php                         ✓ (accessToken + refreshToken)
  Auth/Application/Security/TokenGeneratorInterface.php             ✓
  Auth/Infrastructure/Security/PasswordHash.php                     ✓ (Argon2id, needsRehash)
  Auth/Infrastructure/Security/JWTGenerate.php                      ✓ (RS256, firebase/php-jwt)
  Auth/Infrastructure/Persistence/UserRepository.php                ✓ (PDO MySQL, UPSERT)
  Auth/Infrastructure/Persistence/VerificationTokenRepository.php   ✓
  Auth/Infrastructure/Persistence/RefreshTokenRepository.php        ✓
  Shared/Application/Port/EventListenerInterface.php                ✓
  Auth/Infrastructure/Http/Controllers/                             pendiente
  Auth/Infrastructure/EventDispatcher/EventDispatcher.php          ✓
  Auth/Infrastructure/Http/Controllers/RegisterController.php       ✓
  Auth/Infrastructure/Http/Controllers/AccountConfirmController.php ✓
  Auth/Infrastructure/Http/Controllers/LoginController.php          ← SIGUIENTE
  Auth/Infrastructure/Http/Controllers/LogoutController.php         pendiente
  Auth/Infrastructure/Http/Controllers/LogoutAllController.php      pendiente
  Auth/Infrastructure/Http/Controllers/RefreshController.php        pendiente
  Auth/Infrastructure/Http/Controllers/PasswordRecoveryController.php pendiente
  Auth/Infrastructure/Http/Controllers/ResetPasswordController.php  pendiente
  Shared/Infrastructure/Http/Response.php                          ✓
  Auth/Infrastructure/EventListener/SendEmailConfirmation.php       pendiente
  Shared/Infrastructure/Mailer/SmtpMailer.php                       ✓ (PHPMailer, STARTTLS, try/finally para smtpClose)
  Auth/Infrastructure/EventListener/SendEmailConfirmation.php       ✓
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
- Transacciones via `TransactionManagerInterface` port en `Shared/Application/Port/`
- Eventos se despachan DESPUÉS del commit (fuera de la transacción)
- El repositorio reconstituye entidades — el Use Case nunca llama a `reconstitute()` directamente
- Repositorios tienen método privado `self::reconstitute()` para evitar duplicación
- `isVerified` se guarda como int (0/1) en MySQL, se castea a bool al reconstituir
- `TokenExpiration::isExpired()` retorna bool (CQS), el caller lanza la excepción
- JWT payload contiene `sub` (UserId) + `exp` como Unix timestamp (OWASP — mínimo PII)
- JWT firmado con RS256 (asimétrico — clave pública compartible con terceros)
- Token rotation implementado: al renovar, se elimina el refresh token viejo y se genera uno nuevo
- `needsRehash()` en `PasswordHashInterface` — Login rehashea silenciosamente, fallo no rompe el login
- Login: mensaje específico "cuenta no confirmada" (UX > OWASP estricto en este caso)
- `InvalidTokenException` en `Auth/Domain/Exception/` (es regla de dominio, no de aplicación)
- `DomainEventInterface` en `Shared/Domain/Event/` (no es un port — no conecta con infraestructura)
- `UserRegistered` evento: eventId (string) y occurredAt (DateTimeImmutable) generados internamente
- `PasswordRecoveryRequested` evento despachado desde Use Case (no entidad — entidad no conoce tokens)
- `PasswordRecovery` (Fase 1): retorno silencioso si email no existe (OWASP — evitar user enumeration)
- `PasswordRecovery` (Fase 1): borra tokens previos del mismo tipo antes de crear el nuevo
- `ResetPassword` (Fase 2): valida tokenType + expiración, borra token + todas las sesiones activas
- `MailDTO` con `array $recipients` para soportar múltiples destinatarios
- `EventDispatcherInterface` y `TransactionManagerInterface` en `Shared/Application/Port/`
- Experimento planeado: usar MySQL en un equipo y PostgreSQL en otro para validar la arquitectura hexagonal
