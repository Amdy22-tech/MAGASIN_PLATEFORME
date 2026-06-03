-- Base de données AMDY'S - Plateforme de vente informatique
CREATE DATABASE IF NOT EXISTS amdys_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE amdys_db;

-- Table utilisateurs (vendeurs)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('vendeur','admin') DEFAULT 'vendeur',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table clients
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table articles
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    categorie VARCHAR(100),
    image_url VARCHAR(500),
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table commandes
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente','confirmee','livree','annulee') DEFAULT 'en_attente',
    montant_total DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- Table contenir (lignes de commande)
CREATE TABLE IF NOT EXISTS contenir (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    article_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- Table ventes
CREATE TABLE IF NOT EXISTS ventes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    user_id INT NOT NULL,
    date_vente DATETIME DEFAULT CURRENT_TIMESTAMP,
    montant DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ===================== DONNÉES INITIALES =====================

-- Vendeurs (mot de passe: vendeur123 hashé)
INSERT INTO users (nom, prenom, email, mot_de_passe, role) VALUES
('MOUMOUINI ', 'Amdiyatou', 'amdi@amdys.bj', 'Amdi123@', 'admin'),
('BONI ', 'Mariam', 'mariam@amdys.bj', 'Mrm123@', 'vendeur'),
('MAHAMOUD', 'Wafah', 'wafahmhd@amdys.bj', 'Waf123@', 'vendeur'),
('KORA', 'Aicha', 'aichakora@amdys.bj', 'Aicha123@', 'vendeur'),


-- Clients (mot de passe: client123 hashé)
INSERT INTO clients (nom, prenom, email, telephone, adresse, mot_de_passe) VALUES
('AZONHIHO', 'Fabrice', 'fabrice.azonhiho@gmail.com', '97123456', 'Cotonou, Akpakpa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uZutLn/GW'),
('GNONLONFOUN', 'Stéphanie', 'stephanie.g@gmail.com', '96234567', 'Porto-Novo, Ouando', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uZutLn/GW'),
('HOUNSOU', 'Théodore', 'theodore.h@yahoo.fr', '95345678', 'Abomey-Calavi, Godomey', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uZutLn/GW'),
('LOKO', 'Béatrice', 'beatrice.loko@gmail.com', '94456789', 'Cotonou, Cadjehoun', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uZutLn/GW'),
('AHOUANNOU', 'Patrick', 'patrick.ahouannou@gmail.com', '93567890', 'Parakou, Centre', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uZutLn/GW'),
('CODJO', 'Nadège', 'nadege.codjo@hotmail.com', '92678901', 'Cotonou, Fidjrossè', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uZutLn/GW'),
('TOSSOU', 'Gervais', 'gervais.tossou@gmail.com', '91789012', 'Ouidah, Centre', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uZutLn/GW'),
('AGBLA', 'Mariam', 'mariam.agbla@gmail.com', '97890123', 'Cotonou, Sainte-Rita', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uZutLn/GW'),
('ZINSOU', 'Hervé', 'herve.zinsou@gmail.com', '96901234', 'Abomey-Calavi, Togba', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uZutLn/GW'),
('BOCOVO', 'Claudine', 'claudine.bocovo@gmail.com', '95012345', 'Cotonou, Dandji', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uZutLn/GW');

-- Articles informatiques avec vraies images (URLs publiques)
INSERT INTO articles (nom, description, prix, stock, categorie, image_url) VALUES
('MacBook Pro 14" M3', 'Processeur Apple M3, 16 Go RAM, 512 Go SSD, écran Liquid Retina XDR', 950000, 8, 'Ordinateurs portables', 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400'),
('Dell XPS 15', 'Intel Core i7-13700H, 16 Go RAM, 512 Go SSD, écran OLED 3.5K', 780000, 5, 'Ordinateurs portables', 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=400'),
('HP Pavilion 15', 'Intel Core i5-1235U, 8 Go RAM, 256 Go SSD, écran FHD', 320000, 15, 'Ordinateurs portables', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400'),
('iPhone 15 Pro Max', '256 Go, Titanium Black, puce A17 Pro, écran Super Retina XDR', 680000, 10, 'Smartphones', 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=400'),
('Samsung Galaxy S24 Ultra', '256 Go, Titanium Gray, S Pen inclus, écran Dynamic AMOLED 2X', 620000, 12, 'Smartphones', 'https://images.unsplash.com/photo-1707920088442-52e32b8e26d0?w=400'),
('iPad Pro 12.9" M2', '256 Go WiFi, écran Liquid Retina XDR, compatible Apple Pencil', 520000, 7, 'Tablettes', 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400'),
('Écran LG UltraWide 34"', 'Résolution 3440x1440, IPS, 144Hz, HDR10, USB-C', 280000, 6, 'Écrans', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400'),
('Samsung Odyssey G7 27"', 'Résolution 2560x1440, VA, 240Hz, 1ms, HDR600, Gaming', 310000, 4, 'Écrans', 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400'),
('Clavier Logitech MX Keys', 'Sans fil, rétroéclairé, compatible Windows/Mac/Linux', 45000, 20, 'Périphériques', 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400'),
('Souris Logitech MX Master 3', 'Sans fil, ergonomique, 4000 DPI, molette MagSpeed', 38000, 25, 'Périphériques', 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400'),
('Casque Sony WH-1000XM5', 'Réduction de bruit active, 30h autonomie, Bluetooth 5.2', 125000, 9, 'Audio', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400'),
('SSD Samsung 990 Pro 2To', 'NVMe PCIe 4.0, 7450 Mo/s lecture, 6900 Mo/s écriture', 85000, 18, 'Stockage', 'https://images.unsplash.com/photo-1601737487795-dab272f52420?w=400'),
('Disque dur externe WD 4To', 'USB 3.0, portable, compatible PC/Mac, 5400 RPM', 32000, 22, 'Stockage', 'https://images.unsplash.com/photo-1531492090185-6549c26a1d66?w=400'),
('Routeur ASUS RT-AX88U', 'WiFi 6, AX6000, double bande, 8 ports LAN, AiMesh', 95000, 8, 'Réseau', 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=400'),
('Imprimante HP LaserJet Pro', 'Laser monochrome, WiFi, recto-verso automatique, 30 ppm', 78000, 11, 'Imprimantes', 'https://images.unsplash.com/photo-1612198188060-c7c2a3b66eae?w=400'),
('Webcam Logitech C920 HD', '1080p 30fps, autofocus, microphone stéréo intégré', 28000, 30, 'Périphériques', 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=400'),
('RAM Corsair Vengeance 32Go', 'DDR5 5600MHz, CL36, kit 2x16Go, compatible Intel/AMD', 55000, 14, 'Composants', 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=400'),
('Carte graphique RTX 4070', 'NVIDIA GeForce RTX 4070, 12 Go GDDR6X, DLSS 3.0', 450000, 3, 'Composants', 'https://images.unsplash.com/photo-1587202372616-b43abea06c2a?w=400'),
('Onduleur APC 1500VA', 'UPS Back-UPS Pro, 1500VA/900W, 10 prises, LCD', 65000, 10, 'Alimentation', 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400'),
('Hub USB-C 10-en-1', 'HDMI 4K, USB 3.0 x4, SD/MicroSD, RJ45, USB-C PD 100W', 18500, 35, 'Accessoires', 'https://images.unsplash.com/photo-1625842268584-8f3296236761?w=400');

-- Commandes exemples
INSERT INTO commandes (client_id, statut, montant_total) VALUES
(1, 'livree', 950000),
(2, 'confirmee', 358000),
(3, 'en_attente', 320000),
(4, 'livree', 668500),
(5, 'confirmee', 125000);

-- Lignes de commande (contenir)
INSERT INTO contenir (commande_id, article_id, quantite, prix_unitaire) VALUES
(1, 1, 1, 950000),
(2, 4, 1, 680000),-- correction: example data
(2, 20, 2, 18500),-- hub USB
(3, 3, 1, 320000),
(4, 10, 1, 38000),
(4, 11, 1, 125000),-- casque
(4, 12, 1, 85000),-- SSD
(4, 20, 1, 18500),-- hub
(5, 11, 1, 125000);

-- Ventes
INSERT INTO ventes (commande_id, user_id, montant) VALUES
(1, 2, 950000),
(2, 3, 358000),
(4, 2, 668500),
(5, 4, 125000);
