CREATE TABLE verification_tokens(
    tokenId CHAR(36) NOT NULL,
    tokenValue CHAR(64) NOT NULL,
    tokenExpiration TIMESTAMP NOT NULL,
    tokenType VARCHAR(50) NOT NULL,
    userId CHAR(36) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT PK_verification_token PRIMARY KEY (tokenId),
    CONSTRAINT FK_verification_tokens_users FOREIGN KEY (userId) REFERENCES users(userId) ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;