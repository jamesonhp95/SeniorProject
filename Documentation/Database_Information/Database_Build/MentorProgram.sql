-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 10, 2018 at 07:33 PM
-- Server version: 5.7.24-0ubuntu0.18.04.1
-- PHP Version: 7.2.10-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `MentorProgram`
--

-- --------------------------------------------------------

--
-- Table structure for table `AdminEmailCredential`
--

CREATE TABLE `AdminEmailCredential` (
  `EmailAddress` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Biography`
--

CREATE TABLE `Biography` (
  `EntryID` int(8) UNSIGNED ZEROFILL NOT NULL,
  `UserID` int(8) UNSIGNED ZEROFILL NOT NULL,
  `Details` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Conversation`
--

CREATE TABLE `Conversation` (
  `ConversationID` int(8) UNSIGNED ZEROFILL NOT NULL,
  `OpenDateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ConversationUser`
--

CREATE TABLE `ConversationUser` (
  `ConversationID` int(8) UNSIGNED ZEROFILL NOT NULL,
  `UserID` int(8) UNSIGNED ZEROFILL NOT NULL COMMENT 'UserID of a User that is in this conversation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Education`
--

CREATE TABLE `Education` (
  `EntryID` int(8) NOT NULL,
  `UserID` int(8) NOT NULL,
  `DegreeName` varchar(100) NOT NULL,
  `UniversityName` varchar(100) NOT NULL,
  `Details` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EmailChangeVerification`
--

CREATE TABLE `EmailChangeVerification` (
  `OldEmail` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Hash` varchar(255) NOT NULL COMMENT 'Verification Code for the email',
  `validated` bit(1) NOT NULL DEFAULT b'0' COMMENT 'will change to 1 if link in email has been validated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `EmailVerification`
--

CREATE TABLE `EmailVerification` (
  `Email` varchar(100) NOT NULL,
  `Hash` varchar(255) NOT NULL COMMENT 'Verification Code for the email',
  `validated` bit(1) NOT NULL DEFAULT b'0' COMMENT 'will change to 1 if link in email has been validated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Expertise`
--

CREATE TABLE `Expertise` (
  `EntryID` int(8) NOT NULL,
  `UserID` int(8) NOT NULL,
  `ExpertiseName` varchar(100) NOT NULL,
  `Details` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Mentee`
--

CREATE TABLE `Mentee` (
  `UserID` int(8) UNSIGNED ZEROFILL NOT NULL,
  `EwuID` int(8) UNSIGNED ZEROFILL NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Mentor`
--

CREATE TABLE `Mentor` (
  `UserID` int(8) UNSIGNED ZEROFILL NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `CurNumMentees` int(1) NOT NULL DEFAULT '0',
  `MaxNumMentees` int(8) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `MentorAffinityGroup`
--

CREATE TABLE `MentorAffinityGroup` (
  `EntryID` int(8) NOT NULL,
  `UserID` int(8) NOT NULL,
  `AffinityName` varchar(100) NOT NULL,
  `Details` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `MentorAvailability`
--

CREATE TABLE `MentorAvailability` (
  `EntryID` int(8) NOT NULL,
  `UserID` int(8) NOT NULL,
  `AvailabilityName` varchar(100) NOT NULL,
  `Details` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `MentorPair`
--

CREATE TABLE `MentorPair` (
  `MentorID` int(8) UNSIGNED ZEROFILL NOT NULL,
  `MenteeID` int(8) UNSIGNED ZEROFILL NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Message`
--

CREATE TABLE `Message` (
  `MessageID` int(8) UNSIGNED ZEROFILL NOT NULL,
  `ConversationID` int(8) UNSIGNED ZEROFILL NOT NULL,
  `OwnerID` int(8) UNSIGNED ZEROFILL NOT NULL COMMENT 'Equal to UserID of Owner',
  `ParentMessageID` int(8) UNSIGNED ZEROFILL NOT NULL DEFAULT '00000000' COMMENT 'Equal to MessageID of message if responding, 0 if not in response',
  `OpenDateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ModifiedDateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Content` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `PasswordChangeVerification`
--

CREATE TABLE `PasswordChangeVerification` (
  `Email` varchar(100) NOT NULL,
  `Hash` varchar(255) NOT NULL COMMENT 'Verification Code for the email',
  `validated` bit(1) NOT NULL DEFAULT b'0' COMMENT 'will change to 1 if link in email has been validated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `PrimaryCommunication`
--

CREATE TABLE `PrimaryCommunication` (
  `EntryID` int(8) NOT NULL,
  `UserID` int(8) NOT NULL,
  `CommunicationName` varchar(100) NOT NULL,
  `Details` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Reports`
--

CREATE TABLE `Reports` (
  `EntryID` int(8) NOT NULL,
  `ReporterID` int(8) UNSIGNED ZEROFILL NOT NULL COMMENT 'UserID of person who reported a User',
  `ReportedUserID` int(8) UNSIGNED ZEROFILL NOT NULL COMMENT 'UserID of User who was reported',
  `Details` varchar(4000) NOT NULL COMMENT 'Message from reporter explaining the transgression',
  `ReportDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IsReviewed` int(1) NOT NULL DEFAULT '0' COMMENT '0 means has not been looked at by an admin, 1 means has been reviewed by admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Skill`
--

CREATE TABLE `Skill` (
  `EntryID` int(8) NOT NULL,
  `UserID` int(8) NOT NULL,
  `SkillName` varchar(100) NOT NULL,
  `Details` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE `User` (
  `UserID` int(8) UNSIGNED ZEROFILL NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) DEFAULT NULL,
  `IsMentor` int(1) NOT NULL DEFAULT '0',
  `IsEnabled` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `UserAffiliation`
--

CREATE TABLE `UserAffiliation` (
  `EntryID` int(8) NOT NULL,
  `UserID` int(8) NOT NULL,
  `AffiliationName` varchar(100) NOT NULL,
  `Details` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `WorkExperience`
--

CREATE TABLE `WorkExperience` (
  `EntryID` int(8) NOT NULL,
  `UserID` int(8) NOT NULL,
  `CompanyName` varchar(100) NOT NULL,
  `JobName` varchar(100) NOT NULL,
  `Details` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `AdminEmailCredential`
--
ALTER TABLE `AdminEmailCredential`
  ADD PRIMARY KEY (`EmailAddress`,`Password`);

--
-- Indexes for table `Biography`
--
ALTER TABLE `Biography`
  ADD PRIMARY KEY (`EntryID`),
  ADD UNIQUE KEY `EntryID` (`EntryID`);

--
-- Indexes for table `Conversation`
--
ALTER TABLE `Conversation`
  ADD PRIMARY KEY (`ConversationID`);

--
-- Indexes for table `ConversationUser`
--
ALTER TABLE `ConversationUser`
  ADD PRIMARY KEY (`ConversationID`,`UserID`);

--
-- Indexes for table `Education`
--
ALTER TABLE `Education`
  ADD PRIMARY KEY (`EntryID`);

--
-- Indexes for table `EmailChangeVerification`
--
ALTER TABLE `EmailChangeVerification`
  ADD PRIMARY KEY (`Email`);

--
-- Indexes for table `EmailVerification`
--
ALTER TABLE `EmailVerification`
  ADD PRIMARY KEY (`Email`);

--
-- Indexes for table `Expertise`
--
ALTER TABLE `Expertise`
  ADD PRIMARY KEY (`EntryID`);

--
-- Indexes for table `Mentee`
--
ALTER TABLE `Mentee`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `UserID` (`UserID`),
  ADD UNIQUE KEY `ewuID` (`EwuID`),
  ADD KEY `UserID_2` (`UserID`);

--
-- Indexes for table `Mentor`
--
ALTER TABLE `Mentor`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `UserID` (`UserID`),
  ADD KEY `UserID_2` (`UserID`);

--
-- Indexes for table `MentorAffinityGroup`
--
ALTER TABLE `MentorAffinityGroup`
  ADD PRIMARY KEY (`EntryID`);

--
-- Indexes for table `MentorAvailability`
--
ALTER TABLE `MentorAvailability`
  ADD PRIMARY KEY (`EntryID`);

--
-- Indexes for table `MentorPair`
--
ALTER TABLE `MentorPair`
  ADD PRIMARY KEY (`MentorID`,`MenteeID`);

--
-- Indexes for table `Message`
--
ALTER TABLE `Message`
  ADD PRIMARY KEY (`MessageID`);

--
-- Indexes for table `PasswordChangeVerification`
--
ALTER TABLE `PasswordChangeVerification`
  ADD PRIMARY KEY (`Email`);

--
-- Indexes for table `PrimaryCommunication`
--
ALTER TABLE `PrimaryCommunication`
  ADD PRIMARY KEY (`EntryID`);

--
-- Indexes for table `Reports`
--
ALTER TABLE `Reports`
  ADD PRIMARY KEY (`EntryID`);

--
-- Indexes for table `Skill`
--
ALTER TABLE `Skill`
  ADD PRIMARY KEY (`EntryID`);

--
-- Indexes for table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `UserID` (`UserID`),
  ADD UNIQUE KEY `UserName` (`UserName`),
  ADD UNIQUE KEY `PasswordHash` (`PasswordHash`),
  ADD KEY `UserID_2` (`UserID`);

--
-- Indexes for table `UserAffiliation`
--
ALTER TABLE `UserAffiliation`
  ADD PRIMARY KEY (`EntryID`);

--
-- Indexes for table `WorkExperience`
--
ALTER TABLE `WorkExperience`
  ADD PRIMARY KEY (`EntryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Biography`
--
ALTER TABLE `Biography`
  MODIFY `EntryID` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;
--
-- AUTO_INCREMENT for table `Conversation`
--
ALTER TABLE `Conversation`
  MODIFY `ConversationID` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `Education`
--
ALTER TABLE `Education`
  MODIFY `EntryID` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;
--
-- AUTO_INCREMENT for table `Expertise`
--
ALTER TABLE `Expertise`
  MODIFY `EntryID` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;
--
-- AUTO_INCREMENT for table `MentorAffinityGroup`
--
ALTER TABLE `MentorAffinityGroup`
  MODIFY `EntryID` int(8) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `MentorAvailability`
--
ALTER TABLE `MentorAvailability`
  MODIFY `EntryID` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `Message`
--
ALTER TABLE `Message`
  MODIFY `MessageID` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT for table `PrimaryCommunication`
--
ALTER TABLE `PrimaryCommunication`
  MODIFY `EntryID` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;
--
-- AUTO_INCREMENT for table `Reports`
--
ALTER TABLE `Reports`
  MODIFY `EntryID` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `Skill`
--
ALTER TABLE `Skill`
  MODIFY `EntryID` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;
--
-- AUTO_INCREMENT for table `User`
--
ALTER TABLE `User`
  MODIFY `UserID` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;
--
-- AUTO_INCREMENT for table `UserAffiliation`
--
ALTER TABLE `UserAffiliation`
  MODIFY `EntryID` int(8) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `WorkExperience`
--
ALTER TABLE `WorkExperience`
  MODIFY `EntryID` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
