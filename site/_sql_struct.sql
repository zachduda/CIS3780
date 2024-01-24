SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `auction_proj`
--

-- --------------------------------------------------------

--
-- Table structure for table `Bidder`
--

CREATE TABLE IF NOT EXISTS `Bidder` (
  `BidderID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(75) NOT NULL,
  `Address` varchar(75) NOT NULL,
  `CellNumber` varchar(10) NOT NULL,
  `HomeNumer` varchar(10) NOT NULL,
  `Email` varchar(200) NOT NULL,
  `Paid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`BidderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Category`
--

CREATE TABLE IF NOT EXISTS `Category` (
  `CategoryID` int NOT NULL AUTO_INCREMENT,
  `Description` varchar(75) NOT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Donor`
--

CREATE TABLE IF NOT EXISTS `Donor` (
  `DonorID` int NOT NULL AUTO_INCREMENT,
  `BusinessName` varchar(75) NOT NULL,
  `ContactName` varchar(75) NOT NULL,
  `ContactEmail` varchar(200) NOT NULL,
  `ContactTitle` varchar(75) NOT NULL,
  `Address` varchar(75) NOT NULL,
  `City` varchar(30) NOT NULL,
  `State` varchar(2) NOT NULL,
  `ZipCode` varchar(5) NOT NULL,
  `TaxReceipt` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`DonorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Item`
--

CREATE TABLE IF NOT EXISTS `Item` (
  `ItemID` int NOT NULL,
  `Description` varchar(75) NOT NULL,
  `RetailValue` decimal(10,2) NOT NULL,
  `DonorID` int NOT NULL,
  `LotID` int NOT NULL,
  PRIMARY KEY (`ItemID`),
  KEY `DonorID` (`DonorID`),
  KEY `LotID` (`LotID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Lot`
--

CREATE TABLE IF NOT EXISTS `Lot` (
  `LotID` int NOT NULL AUTO_INCREMENT,
  `Description` varchar(75) NOT NULL,
  `CategoryID` int NOT NULL,
  `WinningBid` decimal(10,2) NOT NULL,
  `WinningBidder` int NOT NULL,
  `Delivered` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`LotID`),
  KEY `CategoryID` (`CategoryID`),
  KEY `WinningBidder` (`WinningBidder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Bidder`
--
ALTER TABLE `Bidder`
  ADD CONSTRAINT `Bidder_ibfk_1` FOREIGN KEY (`BidderID`) REFERENCES `Lot` (`WinningBidder`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Item`
--
ALTER TABLE `Item`
  ADD CONSTRAINT `Item_ibfk_1` FOREIGN KEY (`DonorID`) REFERENCES `Donor` (`DonorID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Item_ibfk_2` FOREIGN KEY (`LotID`) REFERENCES `Lot` (`LotID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Lot`
--
ALTER TABLE `Lot`
  ADD CONSTRAINT `Lot_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `Category` (`CategoryID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
