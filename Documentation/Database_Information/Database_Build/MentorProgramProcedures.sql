DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `ReportUser`(IN `ReporterIDIn` INT(8) UNSIGNED ZEROFILL, IN `ReportedUserIDIn` INT(8) UNSIGNED, IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT '-1 means there is an active report still being reviewed, 0 good'
BEGIN

IF ReporterIDIn = (SELECT ReporterID FROM Reports WHERE ReporterID = ReporterIDIn AND ReportedUserID = ReportedUserIDIn ORDER BY IsReviewed ASC LIMIT 1) THEN
	IF 0 = (SELECT IsReviewed FROM Reports WHERE ReporterID = ReporterIDIn AND ReportedUserID = ReportedUserIDIn ORDER BY IsReviewed ASC LIMIT 1) THEN
    	SELECT -1 AS Confirmation;
    ELSE
    	INSERT INTO Reports(ReporterID,ReportedUserID,Details) VALUE (ReporterIDIn,ReportedUserIDIn,DetailsIn);
    SELECT 0 AS Confirmation;
    END IF;
ELSE
	INSERT INTO Reports(ReporterID,ReportedUserID,Details) VALUE (ReporterIDIn,ReportedUserIDIn,DetailsIn);
    SELECT 0 AS Confirmation;
	
END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `ReviewReport`(IN `EntryIDIn` INT(8) UNSIGNED)
    NO SQL
    COMMENT '0 on success, -1 no report found'
BEGIN

IF EntryIDIn = (SELECT EntryID FROM Reports WHERE EntryID = EntryIDIn) THEN
	UPDATE Reports SET IsReviewed = 1 WHERE EntryID = EntryIDIn;
    SELECT 0 AS Confirmation;
ELSE
	SELECT -1 AS Confirmation;
END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SearchMentor_All`()
    NO SQL
BEGIN
	CREATE TEMPORARY TABLE outputTable(UserID int(8) PRIMARY KEY, Name varchar(201), JobName varchar(100), CompanyName varchar(100), DegreeName varchar(100), UniversityName varchar(100));
    
    INSERT IGNORE INTO outputTable(UserID,Name,JobName,CompanyName,DegreeName,UniversityName) Select UserID,concat(FirstName,' ',LastName) AS Name,JobName,CompanyName,DegreeName,UniversityName FROM Mentor LEFT JOIN User USING(UserID) LEFT JOIN Education USING(UserID) LEFT JOIN WorkExperience USING(UserID) WHERE User.isMentor = 1 AND User.isEnabled = 1;
    
    SELECT * FROM outputTable;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SearchMentors`(IN `valueIn` VARCHAR(100))
    NO SQL
BEGIN
	CREATE TEMPORARY TABLE tempTable(UserID int(8) PRIMARY KEY);
	CREATE TEMPORARY TABLE outputTable(UserID int(8) PRIMARY KEY, Name varchar(201), JobName varchar(100), CompanyName varchar(100), DegreeName varchar(100), UniversityName varchar(100));
	INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM Mentor WHERE FirstName LIKE concat('%', valueIn,'%');
	INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM Mentor WHERE LastName LIKE concat('%', valueIn,'%');
	INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM Mentor WHERE concat(FirstName,' ',LastName) LIKE concat('%', valueIn,'%');
    INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM Education WHERE DegreeName LIKE concat('%', valueIn,'%');
    INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM Education WHERE UniversityName LIKE concat('%', valueIn,'%');
    INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM Expertise WHERE ExpertiseName LIKE concat('%', valueIn,'%');
    INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM Skill WHERE SkillName LIKE concat('%', valueIn,'%');
    INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM UserAffiliation WHERE AffiliationName LIKE concat('%', valueIn,'%');
    INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM WorkExperience WHERE CompanyName LIKE concat('%', valueIn,'%');
    INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM WorkExperience WHERE JobName LIKE concat('%', valueIn,'%');
    INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM PrimaryCommunication WHERE CommunicationName LIKE concat('%', valueIn,'%');
    INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM MentorAffinityGroup WHERE AffinityName LIKE concat('%', valueIn,'%');
    INSERT IGNORE INTO tempTable(UserID) SELECT UserID FROM MentorAvailability WHERE AvailabilityName LIKE concat('%', valueIn,'%');
    
    INSERT IGNORE INTO outputTable(UserID,Name,JobName,CompanyName,DegreeName,UniversityName) Select UserID,concat(FirstName,' ',LastName) AS Name,JobName,CompanyName,DegreeName,UniversityName FROM tempTable LEFT JOIN Mentor USING(UserID) LEFT JOIN User USING(UserID) LEFT JOIN Education USING(UserID) LEFT JOIN WorkExperience USING(UserID) WHERE User.isMentor = 1 AND User.isEnabled = 1;
	Select * from outputTable;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetConversationDenied`(IN `ConversationIDIn` INT(8) UNSIGNED)
    NO SQL
BEGIN
DECLARE Confirmation INT;

IF ConversationIDIn = (SELECT ConversationID FROM Conversation WHERE ConversationID = ConversationIDIn) 
THEN
	UPDATE Conversation
    SET status = 3
    WHERE ConversationID = ConversationIDIn;
    SET Confirmation = 0;
ELSE
	SET Confirmation = -1;
END IF;

Select Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CheckIsEmailAvailable`(IN `EmailIn` VARCHAR(100))
    NO SQL
    COMMENT '0=email is already used, 1=email is free'
BEGIN

IF EmailIn = (SELECT UserName FROM User WHERE EmailIn = UserName LIMIT 1) THEN
	SELECT 0 AS Confirmation;
ELSE
	SELECT 1 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetUserAffiliation`(IN `UserIDIn` INT(8), IN `AffiliationNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT 'EntryID of new affiliation if success, -1 as EntryID if no User'
BEGIN

IF UserIDIn = (SELECT UserID FROM User WHERE UserID = UserIDIn) THEN
	INSERT INTO UserAffiliation (UserID,AffiliationName,Details) VALUE (UserIDIn,AffiliationNameIn,DetailsIn);
    SELECT LAST_INSERT_ID() AS EntryID;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetUserAvailability`(IN `UserIDIn` INT(8), IN `AvailabilityNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT 'EntryID of new Availability if success, -1 as EntryID if no User'
BEGIN

IF UserIDIn = (SELECT UserID FROM User WHERE UserID = UserIDIn) THEN
	INSERT INTO MentorAvailability (UserID,AvailabilityName,Details) VALUE (UserIDIn,AvailabilityNameIn,DetailsIn);
    SELECT LAST_INSERT_ID() AS EntryID;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetUserBiography`(IN `UserIDIn` INT(8), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT 'EntryID if success, -1 as EntryID if fail, only 1 bio per user'
BEGIN

IF UserIDIn = (SELECT UserID FROM User WHERE USerID = UserIDIn AND UserIDIn NOT IN (SELECT UserID FROM Biography)) THEN
	INSERT INTO Biography(UserID,Details) VALUE(UserIDIn,DetailsIn);
    SELECT LAST_INSERT_ID() AS EntryID;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetUserEducation`(IN `UserIDIn` INT(8), IN `DegreeNameIn` VARCHAR(100), IN `UniversityNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
BEGIN

IF UserIDIn = (SELECT UserID FROM User WHERE UserID = UserIDIn) THEN
	INSERT INTO Education (UserID,DegreeName,UniversityName,Details) VALUE (UserIDIn,DegreeNameIn,UniversityNameIn,DetailsIn);
    SELECT LAST_INSERT_ID() AS EntryID;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetUserEnabled`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT 'returns 0 if successful, -1 if user did not exist'
BEGIN
DECLARE Confirmation INT;
IF UserIDIn = (SELECT UserID FROM User WHERE User.UserID = UserIDIn) 
THEN
	UPDATE User
    SET IsEnabled = 1
    WHERE UserID = UserIDIn;
    SET Confirmation = 0;
    SELECT Confirmation;
ELSE
	SET Confirmation = -1;
    SELECT Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CheckPasswordChangeValidated`(IN `EmailIn` VARCHAR(100), IN `HashIn` VARCHAR(255))
    NO SQL
    COMMENT '0badHash,1=alreadyValidated,2=noEmailFound,3=passwordChangeReady'
BEGIN

IF EmailIn = (SELECT Email FROM PasswordChangeVerification WHERE Email = EmailIn) THEN
	IF 0 = (SELECT validated FROM PasswordChangeVerification WHERE Email = EmailIn) THEN
    	IF HashIn = (SELECT Hash FROM PasswordChangeVerification WHERE Email = EmailIn) THEN
        	SELECT 3 AS Confirmation;
        ELSE
        	SELECT 0 AS Confirmation;
   		END IF;
    ELSE
    	SELECT 1 AS Confirmation;
    END IF;
ELSE
	SELECT 2 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CheckUserVerified`(IN `UserIdIn` INT(8))
    NO SQL
BEGIN
DECLARE varEmail varchar(100);         
SET varEmail = "Generic email";
SET varEmail =  (SELECT Username FROM User WHERE UserID = UserIDIn);

IF varEmail = (SELECT Email FROM EmailVerification WHERE Email = varEmail) THEN
	SELECT validated FROM EmailVerification WHERE Email = varEmail;
ELSE
	SELECT -1 AS validated;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetUserExpertise`(IN `UserIDIn` INT(8), IN `ExpertiseNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT 'EntryID of new Expertise if success, -1 as EntryID if no User'
BEGIN

IF UserIDIn = (SELECT UserID FROM User WHERE UserID = UserIDIn) THEN
	INSERT INTO Expertise (UserID,ExpertiseName,Details) VALUE (UserIDIn,ExpertiseNameIn,DetailsIn);
    SELECT LAST_INSERT_ID() AS EntryID;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetUserPrimaryCommunication`(IN `UserIDIn` INT(8), IN `CommunicationNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT 'EntryID of new PrimaryCommunication if success, -1 as EntryID if no User'
BEGIN

IF UserIDIn = (SELECT UserID FROM User WHERE UserID = UserIDIn) THEN
	INSERT INTO PrimaryCommunication (UserID,CommunicationName,Details) VALUE (UserIDIn,CommunicationNameIn,DetailsIn);
    SELECT LAST_INSERT_ID() AS EntryID;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetUserSkill`(IN `UserIDIn` INT(8), IN `SkillNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT 'EntryID of new Skill if success, -1 as EntryID if no User'
BEGIN

IF UserIDIn = (SELECT UserID FROM User WHERE UserID = UserIDIn) THEN
	INSERT INTO Skill (UserID,SkillName,Details) VALUE (UserIDIn,SkillNameIn,DetailsIn);
    SELECT LAST_INSERT_ID() AS EntryID;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetUserWorkExperience`(IN `UserIDIn` INT(8), IN `CompanyNameIn` VARCHAR(100), IN `JobNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
BEGIN

IF UserIDIn = (SELECT UserID FROM User WHERE UserID = UserIDIn) THEN
	INSERT INTO WorkExperience (UserID,CompanyName,JobName,Details) VALUE (UserIDIn,CompanyNameIn,JobNameIn,DetailsIn);
    SELECT LAST_INSERT_ID() AS EntryID;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SuspendUser`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT '0 success, -1 no user found'
BEGIN

IF UserIDIn = (SELECT UserID From User WHERE UserID = UserIDIn) THEN
	UPDATE User SET IsEnabled = 2 WHERE UserID = UserIDIn;
    SELECT 0 AS Confirmation;
ELSE
	SELECT -1 AS Confirmation;
END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `TestBoolean`()
    NO SQL
BEGIN

SELECT false;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `ValidatePasswordChange`(IN `EmailIn` VARCHAR(100))
    NO SQL
    COMMENT '0=passwordChangeNowValidated,1=NoEmailFound'
BEGIN

IF EmailIn = (SELECT Email FROM PasswordChangeVerification WHERE Email = EmailIn) THEN
	UPDATE PasswordChangeVerification
    SET validated = 1
    WHERE Email = EmailIn;
    SELECT 0 AS Confirmation;
ELSE
	SELECT -1 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CreateConversation`()
    NO SQL
    COMMENT 'return new conversationID or -1 if error'
BEGIN
DECLARE ConversationIDOut INT DEFAULT -1;

INSERT INTO Conversation () VALUE ();
SET ConversationIDOut = LAST_INSERT_ID();

SELECT ConversationIDOut AS ConversationID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CreateConversationUser`(IN `UserIDIn` INT(8), IN `ConversationIDIn` INT(8))
    NO SQL
    COMMENT 'return -1 user missing, -2 conversation missing'
BEGIN
DECLARE Confirmation INT DEFAULT -1;

IF UserIDIn = (SELECT UserID FROM User WHERE UserID = UserIDIn) THEN
	SET Confirmation = -2;
    
    IF ConversationIDIn = (SELECT ConversationID FROM Conversation WHERE ConversationID = ConversationIDIn) THEN    
    SET Confirmation = 0;
    INSERT INTO ConversationUser (ConversationID, UserID) VALUE (ConversationIDIn, UserIDIn);
	END IF;
    
END IF;
SELECT Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetUserDisabled`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT 'returns 0 if successful, -1 if user did not exist'
BEGIN
DECLARE Confirmation INT;
IF UserIDIn = (SELECT UserID FROM User WHERE User.UserID = UserIDIn) 
THEN
	UPDATE User
    SET IsEnabled = 0
    WHERE UserID = UserIDIn;
    SET Confirmation = 0;
ELSE
	SET Confirmation = -1;
END IF;
	SELECT Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CreateEmailChangeVerification`(IN `EmailIn` VARCHAR(100), IN `HashIn` VARCHAR(255), IN `OldEmailIn` VARCHAR(100))
    NO SQL
    COMMENT 'Used to generate new Hash for email verification'
BEGIN

IF OldEmailIn = (SELECT OldEmail FROM EmailChangeVerification WHERE OldEmail = OldEmailIn) THEN
	UPDATE EmailChangeVerification SET Hash = HashIn WHERE OldEmail = OldEmailIn;
    UPDATE EmailChangeVerification SET validated = 0 WHERE OldEmail = OldEmailIn;
ELSE
	INSERT INTO EmailChangeVerification(OldEmail,Email,Hash) Value (OldEmailIn, EmailIn, HashIn);
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetConversationAccepted`(IN `ConversationIDIn` INT(8))
    NO SQL
    COMMENT 'returns 0 as Confirmation if success, -1 no conversation found'
BEGIN
DECLARE Confirmation INT;

IF ConversationIDIn = (SELECT ConversationID FROM Conversation WHERE ConversationID = ConversationIDIn) 
THEN
	UPDATE Conversation
    SET status = 0
    WHERE ConversationID = ConversationIDIn;
    SET Confirmation = 0;
ELSE
	SET Confirmation = -1;
END IF;

Select Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CreateMentor`(IN `FirstName` VARCHAR(50), IN `LastName` VARCHAR(50), IN `Email` VARCHAR(100), IN `MaxNumMenteesIn` INT(8))
    NO SQL
    COMMENT 'Returns new Mentor''s UserID or 0 if user already existed'
BEGIN
DECLARE UserIDOut INT DEFAULT 0;

SELECT UserID INTO UserIDOut
FROM User
WHERE User.UserName = Email;

IF UserIDOut = 0 THEN
	INSERT INTO User (IsEnabled,IsMentor,UserID,UserName) VALUES (0,1,NULL,Email);
    
    SELECT UserID INTO UserIDOut
	FROM User
	WHERE User.UserName = Email;
    
    INSERT INTO Mentor (Email, FirstName, LastName, UserID, MaxNumMentees) VALUE (Email, FirstName, LastName, UserIDOut, MaxNumMenteesIn);
    
	IF Email LIKE '%@ewu.edu' THEN
    	UPDATE User
        SET isEnabled = 1
        WHERE UserID = UserIDOut;
    END IF;    
ELSE
	SET UserIDOut = 0;
END IF;
SELECT UserIDOut AS UserID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetUserAffinityGroup`(IN `UserIDIn` INT(8), IN `AffinityNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT 'EntryID of new Affinity if success, -1 as EntryID if no User'
BEGIN

IF UserIDIn = (SELECT UserID FROM User WHERE UserID = UserIDIn) THEN
	INSERT INTO MentorAffinityGroup (UserID,AffinityName,Details) VALUE (UserIDIn,AffinityNameIn,DetailsIn);
    SELECT LAST_INSERT_ID() AS EntryID;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CreateEmailVerification`(IN `EmailIn` VARCHAR(100), IN `HashIn` VARCHAR(255))
    NO SQL
    COMMENT 'Used to generate new Hash for email verification'
BEGIN

IF EmailIn = (SELECT Email FROM EmailVerification WHERE Email = EmailIn) THEN
	UPDATE EmailVerification SET Hash = HashIn WHERE Email = EmailIn;
    UPDATE EmailVerification SET validated = 0 WHERE Email = EmailIn;
ELSE
	INSERT INTO EmailVerification(Email,Hash) Value (EmailIN, HashIn);
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CreateMessage`(IN `ConversationIDIn` INT(8), IN `OwnerIDIn` INT(8), IN `ParentMessageIDIn` INT(8), IN `MessageContentIn` VARCHAR(4000) CHARSET utf8)
    NO SQL
BEGIN
DECLARE MessageIDOut INT DEFAULT -1;

INSERT INTO Message (ConversationID, OwnerID, ParentMessageID, Content) VALUE (ConversationIDIn, OwnerIDIn, ParentMessageIDIn, MessageContentIn);

SET MessageIDOut = LAST_INSERT_ID();
SELECT MessageIDOut AS MessageID;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `DeleteMentee`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT 'Returns 0 on proper deletion, -1 on no entry/wrong user type'
BEGIN
DECLARE UserIDOut INT;
IF UserIDIn = (SELECT UserID FROM Mentee Where Mentee.UserID = UserIDIn) THEN
	SET UserIDOut = 0;
    DELETE FROM Mentee Where Mentee.UserID = UserIDIn;
    DELETE FROM User WHERE User.UserID = UserIDIn;
ELSE
	SET UserIDOut = -1;
END IF;
SELECT UserIDOut AS UserID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `DeleteMentor`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT 'Returns 0 on proper deletion, -1 on no entry/wrong user type'
BEGIN
DECLARE UserIDOut INT;

IF UserIDIn = (SELECT UserID FROM Mentor Where Mentor.UserID = UserIDIn) THEN
	SET UserIDOut = 0;
    DELETE FROM Mentor Where Mentor.UserID = UserIDIn;
    DELETE FROM User WHERE User.UserID = UserIDIn;
    DELETE FROM Biography WHERE UserID = UserIDIn;
    DELETE FROM Education WHERE UserID = UserIDIn;
    DELETE FROM Expertise WHERE UserID = UserIDIn;
    DELETE FROM MentorAffinityGroup WHERE UserID = UserIDIn;
    DELETE FROM MentorAvailability WHERE UserID = UserIDIn;
    DELETE FROM MentorPair WHERE MentorID = UserIDIn;
    DELETE FROM PrimaryCommunication WHERE UserID = UserIDIn;
    DELETE FROM Reports WHERE ReportedUserID = UserIDIn;
    DELETE FROM Skill WHERE UserID = UserIDIn;
    DELETE FROM UserAffiliation WHERE UserID = UserIDIn;
    DELETE FROM WorkExperience WHERE UserID = UserIDIn;
ELSE
	SET UserIDOut = -1;
END IF;
SELECT UserIDOut AS UserID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `DeleteMentorPair`(IN `MentorIDIn` INT(8), IN `MenteeIDIn` INT(8))
    NO SQL
BEGIN
DECLARE Confirmation int(1) DEFAULT -1;

IF (MentorIDIn = (SELECT MentorID FROM MentorPair WHERE MentorID = MentorIDIn AND MenteeID = MenteeIDIn)) THEN
	SET Confirmation = 0;
	DELETE FROM MentorPair WHERE MentorID = MentorIDIn AND MenteeID = MenteeIDIn;
	UPDATE Mentor SET CurNumMentees=CurNumMentees-1 WHERE UserID = MentorIDIn;
END IF;

SELECT Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `DeleteUserAffiliation`(IN `EntryIDIn` INT(8))
    NO SQL
BEGIN

DELETE FROM UserAffiliation WHERE EntryID = EntryIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `DeleteUserAffinityGroup`(IN `EntryIDIn` INT(8))
    NO SQL
BEGIN

DELETE FROM MentorAffinityGroup WHERE EntryID = EntryIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `DeleteUserAvailability`(IN `UserIDIn` INT(8))
    NO SQL
BEGIN

DELETE FROM MentorAvailability WHERE EntryID = EntryIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `DeleteUserBiography`(IN `EntryIDIn` INT(8))
    NO SQL
BEGIN

DELETE FROM Biography WHERE EntryID = EntryIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `DeleteUserEducation`(IN `EntryIDIn` INT(8))
    NO SQL
BEGIN

DELETE FROM Education WHERE EntryID = EntryIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `DeleteUserExpertise`(IN `EntryIDIn` INT(8))
    NO SQL
BEGIN

DELETE FROM Expertise WHERE EntryID = EntryIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CreateMentorPair`(IN `MentorIDIn` INT(8), IN `MenteeIDIn` INT(8))
    NO SQL
BEGIN
DECLARE Confirmation int(1) DEFAULT -1;

IF MentorIDIn = (SELECT UserID FROM Mentor WHERE UserID = MentorIDIn) THEN
	SET Confirmation = -2;
    IF MenteeIDIn = (SELECT UserID FROM Mentee WHERE UserID = MenteeIDIn) THEN
		SET Confirmation = -3;
    	IF (SELECT MaxNumMentees FROM Mentor WHERE UserID = MentorIDIn) > (SELECT CurNumMentees FROM Mentor WHERE UserID = MentorIDIn) THEN
        	SET Confirmation = 0;
    		INSERT INTO MentorPair (MentorID,MenteeID) VALUE (MentorIDIn,MenteeIDIn);
			UPDATE Mentor SET CurNumMentees=CurNumMentees+1 WHERE UserID = MentorIDIn;
    	END IF;
	END IF;
END IF;

SELECT Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CreateMentee`(IN `FirstName` VARCHAR(50), IN `LastName` VARCHAR(50), IN `Email` VARCHAR(100), IN `UserName` VARCHAR(50), IN `EwuIDIn` INT(8))
    NO SQL
    COMMENT 'Returns new Mentee UserID or 0 if user already existed'
BEGIN
DECLARE UserIDOut INT DEFAULT 0;

SELECT UserID INTO UserIDOut
FROM User
WHERE User.UserName = UserName;

IF UserIDOut = 0 THEN
	INSERT INTO User (IsEnabled,IsMentor,UserID,UserName) VALUES (1,0,NULL,UserName);
    
    SELECT UserID INTO UserIDOut
	FROM User
	WHERE User.UserName = UserName;
    
    INSERT INTO Mentee (Email, FirstName, LastName, UserID, EwuID) VALUE (Email, FirstName, LastName, UserIDOut, EwuIDIn);
ELSE
	SET UserIDOut = 0;
END IF;
SELECT UserIDOut AS UserID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `DeleteUserPrimaryCommunication`(IN `EntryIDIn` INT(8))
    NO SQL
BEGIN

DELETE FROM PrimaryCommunication WHERE EntryID = EntryIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `DeleteUserSkill`(IN `EntryIDIn` INT(8))
    NO SQL
BEGIN

DELETE FROM Skill WHERE EntryID = EntryIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CreatePasswordChangeVerification`(IN `EmailIn` VARCHAR(100), IN `HashIn` VARCHAR(255))
    NO SQL
BEGIN

IF EmailIn = (SELECT Email FROM PasswordChangeVerification WHERE Email = EmailIn) THEN
	UPDATE PasswordChangeVerification SET Hash = HashIn WHERE Email = EmailIn;
    UPDATE PasswordChangeVerification SET validated = 0 WHERE Email = EmailIn;
ELSE
	INSERT INTO PasswordChangeVerification(Email,Hash) Value (EmailIN, HashIn);
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditMentorMaxNumMentee`(IN `UserIDIn` INT(8), IN `MaxNumMenteesIn` INT(8))
    NO SQL
BEGIN
DECLARE Confirmation INT;
IF UserIDIn = (SELECT UserID FROM Mentor WHERE UserID = UserIDIn) 
THEN
	UPDATE Mentor
    SET MaxNumMentees = MaxNumMenteesIn
    WHERE UserID = UserIDIn;
    SET Confirmation = 0;
ELSE
	SET Confirmation = -1;
END IF;

Select Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `DeleteWorkExperience`(IN `EntryIDIn` INT(8))
    NO SQL
BEGIN

DELETE FROM WorkExperience WHERE EntryID = EntryIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditMessageContent`(IN `MessageIDIn` INT(8), IN `MessageContentIn` VARCHAR(4000))
    NO SQL
BEGIN
DECLARE Confirmation int(1) DEFAULT -1;

IF MessageIDIn = (SELECT MessageID FROM Message WHERE MessageID = MessageIDIn) THEN
	SET Confirmation = 0;
    UPDATE Message SET Content = MessageContentIn WHERE MessageID = MessageIDIn;
END IF;

SELECT Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditUserBiography`(IN `EntryIDIn` INT(8), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT '0 on success, -1 on fail'
BEGIN

IF EntryIDIn = (SELECT EntryID FROM Biography WHERE EntryID = EntryIDIn) THEN
	UPDATE Biography SET Details = DetailsIn WHERE EntryID = EntryIDIn;
    SELECT 0 AS Confirmation;
ELSE
	SELECT -1 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditUserAffinityGroup`(IN `EntryIDIn` INT(8), IN `AffinityNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT '0 on success, -1 on fail'
BEGIN

IF EntryIDIn = (SELECT EntryID FROM MentorAffinityGroup WHERE EntryID = EntryIDIn) THEN
	UPDATE MentorAffinityGroup SET AffinityName = AffinityNameIn, Details = DetailsIn WHERE EntryID = EntryIDIn;
    SELECT 0 AS Confirmation;
ELSE
	SELECT -1 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditUserEmail`(IN `UserIDIn` INT(8), IN `EmailIn` VARCHAR(100))
    NO SQL
    COMMENT 'check if email if free first with CheckIsEmailAvailable($email)'
BEGIN
DECLARE Confirmation INT;

IF UserIDIn = (SELECT UserID FROM Mentor WHERE UserID = UserIDIn) THEN
	UPDATE Mentor
    SET Email = EmailIn
    WHERE UserID = UserIDIn;
        
    UPDATE User
    SET UserName = EmailIn
    WHERE UserID = UserIDIn;
    
    SET Confirmation = 0;
ELSEIF UserIDIn = (SELECT UserID FROM Mentee WHERE UserID = UserIDIn) THEN
	UPDATE Mentee
    SET Email = EmailIn
    WHERE UserID = UserIDIn;
    SET Confirmation = 0;
ELSE
	SET Confirmation = -1;
END IF;
	SELECT Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditUserAvailability`(IN `EntryIDIn` INT(8), IN `AvailabilityNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT '0 on success, -1 on fail'
BEGIN

IF EntryIDIn = (SELECT EntryID FROM MentorAvailability WHERE EntryID = EntryIDIn) THEN
	UPDATE MentorAvailability SET AvailabilityName = AvailabilityNameIn, Details = DetailsIn WHERE EntryID = EntryIDIn;
    SELECT 0 AS Confirmation;
ELSE
	SELECT -1 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditUserAffiliation`(IN `EntryIDIn` INT(8), IN `AffiliationNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT '0 on success, -1 on fail'
BEGIN

IF EntryIDIn = (SELECT EntryID FROM UserAffiliation WHERE EntryID = EntryIDIn) THEN
	UPDATE UserAffiliation SET AffiliationName = AffiliationNameIn, Details = DetailsIn WHERE EntryID = EntryIDIn;
    SELECT 0 AS Confirmation;
ELSE
	SELECT -1 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditUserEducation`(IN `EntryIDIn` INT(8), IN `DegreeNameIn` VARCHAR(100), IN `UniversityNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT '0 on success, -1 on fail'
BEGIN

IF EntryIDIn = (SELECT EntryID FROM Education WHERE EntryID = EntryIDIn) THEN
	UPDATE Education SET DegreeName = DegreeNameIn, UniversityName = UniversityNameIn, Details = DetailsIn WHERE EntryID = EntryIDIn;
    SELECT 0 AS Confirmation;
ELSE
	SELECT -1 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditUserExpertise`(IN `EntryIDIn` INT(8), IN `ExpertiseNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT '0 on success, -1 on fail'
BEGIN

IF EntryIDIn = (SELECT EntryID FROM Expertise WHERE EntryID = EntryIDIn) THEN
	UPDATE Expertise SET ExpertiseName = ExpertiseNameIn, Details = DetailsIn WHERE EntryID = EntryIDIn;
    SELECT 0 AS Confirmation;
ELSE
	SELECT -1 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditUserLastName`(IN `UserIDIn` INT(8), IN `LastNameIn` VARCHAR(100))
    NO SQL
BEGIN
DECLARE Confirmation INT;

IF UserIDIn = (SELECT UserID FROM Mentor WHERE UserID = UserIDIn) THEN
	UPDATE Mentor
    SET LastName = LastNameIn
    WHERE UserID = UserIDIn;
    SET Confirmation = 0;
ELSEIF UserIDIn = (SELECT UserID FROM Mentee WHERE UserID = UserIDIn) THEN
	UPDATE Mentor
    SET LastName = LastNameIn
    WHERE UserID = UserIDIn;
    SET Confirmation = 0;
ELSE
	SET Confirmation = -1;
END IF;
	SELECT Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditUserFirstName`(IN `UserIDIn` INT(8), IN `FirstNameIn` VARCHAR(100))
    NO SQL
BEGIN
DECLARE Confirmation INT;

IF UserIDIn = (SELECT UserID FROM Mentor WHERE UserID = UserIDIn) THEN
	UPDATE Mentor
    SET FirstName = FirstNameIn
    WHERE UserID = UserIDIn;
    SET Confirmation = 0;
ELSEIF UserIDIn = (SELECT UserID FROM Mentee WHERE UserID = UserIDIn) THEN
	UPDATE Mentor
    SET FirstName = FirstNameIn
    WHERE UserID = UserIDIn;
    SET Confirmation = 0;
ELSE
	SET Confirmation = -1;
END IF;
	SELECT Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditUserPrimaryCommunication`(IN `EntryIDIn` INT(8), IN `CommunicationNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT '0 on success, -1 on fail'
BEGIN

IF EntryIDIn = (SELECT EntryID FROM PrimaryCommunication WHERE EntryID = EntryIDIn) THEN
	UPDATE PrimaryCommunication SET CommunicationName = CommunicationNameIn, Details = DetailsIn WHERE EntryID = EntryIDIn;
    SELECT 0 AS Confirmation;
ELSE
	SELECT -1 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditUserWorkExperience`(IN `EntryIDIn` INT(8), IN `CompanyNameIn` VARCHAR(100), IN `JobNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT '0 on success, -1 on fail'
BEGIN

IF EntryIDIn = (SELECT EntryID FROM WorkExperience WHERE EntryID = EntryIDIn) THEN
	UPDATE WorkExperience SET JobName = JobNameIn, CompanyName = CompanyNameIn, Details = DetailsIn WHERE EntryID = EntryIDIn;
    SELECT 0 AS Confirmation;
ELSE
	SELECT -1 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetBannedUsers`()
    NO SQL
BEGIN

SELECT UserID, UserName FROM User WHERE IsEnabled = 2;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `EditUserSkill`(IN `EntryIDIn` INT(8), IN `SkillNameIn` VARCHAR(100), IN `DetailsIn` VARCHAR(4000))
    NO SQL
    COMMENT '0 on success, -1 on fail'
BEGIN

IF EntryIDIn = (SELECT EntryID FROM Skill WHERE EntryID = EntryIDIn) THEN
	UPDATE Skill SET SkillName = SkillNameIn, Details = DetailsIn WHERE EntryID = EntryIDIn;
    SELECT 0 AS Confirmation;
ELSE
	SELECT -1 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetConversation`(IN `ConversationIDIn` INT(8))
    NO SQL
BEGIN
SELECT OwnerID,Content,ModifiedDateTime FROM Message WHERE ConversationID = ConversationIDIn ORDER BY MessageID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetAdminEmailCredentials`()
    NO SQL
    COMMENT 'returns EmailAddress,Password'
BEGIN
Select * FROM AdminEmailCredential;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetConversationExists`(IN `UserIDIn` INT(8) UNSIGNED ZEROFILL, IN `ViewIDIn` INT(8) UNSIGNED ZEROFILL)
    NO SQL
    COMMENT 'Returns ConversationID shared by users, -1 if no conversation'
BEGIN
	DECLARE convoID int DEFAULT 0;

	CREATE TEMPORARY TABLE User1Table(ConversationID int(8), UserID int(8));
    CREATE TEMPORARY TABLE User2Table(ConversationID int(8), UserID int(8));
    
    INSERT IGNORE INTO User1Table(ConversationID, UserID) SELECT ConversationID,UserID FROM ConversationUser WHERE UserID = UserIDIn;
    
    INSERT IGNORE INTO User2Table(ConversationID, UserID) SELECT ConversationID,UserID FROM ConversationUser WHERE UserID = ViewIDIn;
    
    SET ConvoID = (SELECT User1Table.ConversationID FROM User1Table JOIN User2Table ON User1Table.ConversationID = User2Table.ConversationID);
    IF ConvoID IS NOT NULL THEN
    	SELECT ConvoID AS ConversationID;
    ELSE
    	SELECT -1 AS ConversationID;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetConversationUsers`(IN `ConversationIDIn` INT(8))
    NO SQL
    COMMENT 'UserIDs of users in the conversation, -1 As UserID if fail'
BEGIN
SELECT UserID FROM ConversationUser WHERE ConversationID = ConversationIDIn;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetConversationPeek`(IN `ConversationIDIn` INT(8))
    NO SQL
    COMMENT 'Most recent message line if success, -1 as MessageID for failure'
BEGIN
DECLARE MessageIDMax INT(8);

SELECT MAX(MessageID) INTO MessageIDMax FROM Message WHERE ConversationID = ConversationIDIn;
IF MessageIDMax IS NULL THEN
	SELECT -1 AS MessageID;
ELSE
	SELECT * FROM Message WHERE ConversationID = ConversationIDIn ORDER BY MessageID DESC LIMIT 5;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jprice13`@`localhost` PROCEDURE `GetIndividualReport`(IN `EntryIDIn` INT)
    NO SQL
BEGIN

SELECT EntryID,ReporterID,ReportedUserID,Details,ReportDate FROM Reports WHERE EntryID = EntryIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetConversationStatus`(IN `ConversationIDIn` INT(8))
    NO SQL
    COMMENT 'returns status value on success, -1 if no conversation found'
BEGIN

IF ConversationIDIn = (SELECT ConversationID FROM Conversation WHERE ConversationID = ConversationIDIn) 
THEN
	SELECT status FROM Conversation WHERE ConversationID = ConversationIDIn;
ELSE
	SELECT -1 AS status;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetMenteesList`(IN `MentorIDIn` INT(8))
    NO SQL
BEGIN

SELECT MenteeID FROM MentorPair WHERE MentorID = MentorIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetMentorsList`(IN `MenteeIDIn` INT(8))
    NO SQL
BEGIN

SELECT MentorID FROM MentorPair WHERE MenteeID = MenteeIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetMenteeUserID_EwuID`(IN `EwuIDIn` INT(8) UNSIGNED ZEROFILL)
    NO SQL
    COMMENT '-1 as PasswordHash if no user, users PasswordHash if success'
BEGIN

IF EwuIDIn = (SELECT EwuID FROM Mentee WHERE EwuID = EwuIDIn) THEN
	SELECT UserID FROM Mentee WHERE EwuID = EwuIDIn;
ELSE
	SELECT -1 AS UserID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetMentor`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT '-1 as UserID if user not found as mentor, Mentor row if success'
BEGIN

IF UserIDIn = (SELECT UserID FROM Mentor WHERE UserID = UserIDIn) THEN
	SELECT * FROM Mentor WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS UserID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetMentee`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT '-1 as UserID if user not found as mentee, Mentee row if success'
BEGIN

IF UserIDIn = (SELECT UserID FROM Mentee WHERE UserID = UserIDIn) THEN
	SELECT * FROM Mentee WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS UserID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetMessage`(IN `MessageIDIn` INT(8))
    NO SQL
    COMMENT 'Raw: Returns Message table entry for the input MessageID '
BEGIN

SELECT * FROM Message WHERE MessageID = MessageIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetMessageContent`(IN `MessageIDIn` INT(8))
    NO SQL
    COMMENT 'returns the content of the message for the input MessageID'
BEGIN

SELECT Content FROM Message WHERE MessageID = MessageIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jprice13`@`localhost` PROCEDURE `GetPendingMentors`()
    NO SQL
BEGIN

CREATE TEMPORARY TABLE firstTable AS (SELECT UserName, UserID FROM User WHERE IsEnabled = 0 AND IsMentor = 1);

SELECT UserName, UserID FROM firstTable JOIN EmailVerification ON firstTable.Username = EmailVerification.Email WHERE EmailVerification.validated = 1;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetReportsNew`()
    NO SQL
BEGIN

SELECT EntryID,ReporterID,ReportedUserID,Details,ReportDate FROM Reports WHERE IsReviewed = 0;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetMessageDateTime`(IN `MessageIDIn` INT(8))
    NO SQL
BEGIN

SELECT OpenDateTime,ModifiedDateTime FROM Message WHERE MessageID = MessageIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUser`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT '-1 as UserID if no user found, User row if success'
BEGIN

IF UserIDIn = (SELECT UserID FROM Mentor WHERE UserID = UserIDIn) THEN
	SELECT UserID,UserName,IsMentor,IsEnabled,concat(FirstName,' ',LastName) AS Name FROM User NATURAL JOIN Mentor WHERE UserID = UserIDIn;
ELSEIF UserIDIn = (SELECT UserID FROM Mentee WHERE UserID = UserIDIn) THEN
	SELECT UserID,UserName,IsMentor,IsEnabled,concat(FirstName,' ',LastName) AS Name FROM User NATURAL JOIN Mentee WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS UserID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserAffiliations`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT 'EntryID,AffiliationName,Details for user, -1 as EntryID on fail'
BEGIN

IF UserIDIn = (SELECT UserID FROM UserAffiliation WHERE UserID = UserIDIn LIMIT 1) THEN
	SELECT EntryID,AffiliationName,Details FROM UserAffiliation WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserAffinityGroups`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT 'EntryID,AffinityName,Details for users AffinityGroups, -1 as EntryID if fail'
BEGIN

IF UserIDIn = (SELECT UserID FROM MentorAffinityGroup WHERE UserID = UserIDIn LIMIT 1) THEN
	SELECT EntryID,AffinityName,Details FROM MentorAffinityGroup WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetRecommendationList`()
    NO SQL
BEGIN
	CREATE TEMPORARY TABLE tempTable(itemName VARCHAR(100) PRIMARY KEY);
    INSERT IGNORE INTO tempTable(itemName) SELECT DegreeName FROM Education;
    INSERT IGNORE INTO tempTable(itemName) SELECT UniversityName FROM Education;
    INSERT IGNORE INTO tempTable(itemName) SELECT ExpertiseName FROM Expertise;
    INSERT IGNORE INTO tempTable(itemName) SELECT SkillName FROM Skill;
    INSERT IGNORE INTO tempTable(itemName) SELECT AffiliationName FROM UserAffiliation;
    INSERT IGNORE INTO tempTable(itemName) SELECT CompanyName FROM WorkExperience;
    INSERT IGNORE INTO tempTable(itemName) SELECT JobName FROM WorkExperience;
    INSERT IGNORE INTO tempTable(itemName) SELECT CommunicationName FROM PrimaryCommunication;
    INSERT IGNORE INTO tempTable(itemName) SELECT AffinityName FROM MentorAffinityGroup;
    INSERT IGNORE INTO tempTable(itemName) SELECT AvailabilityName FROM MentorAvailability;

    Select * FROM tempTable;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserBiography`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT 'Returns EntryID,Details if succcess, -1 as EntryID if fail'
BEGIN

IF UserIDIn = (SELECT UserID FROM Biography WHERE UserID = UserIDIn LIMIT 1) THEN
	SELECT EntryID,Details FROM Biography WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserEducation`(IN `UserIDIn` INT(8))
    NO SQL
BEGIN

IF UserIDIn = (SELECT UserID FROM Education WHERE UserID = UserIDIn LIMIT 1) THEN
	SELECT EntryID,DegreeName,UniversityName,Details FROM Education WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserExpertise`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT 'EntryID,ExpertiseName,Details for users Expertise, -1 as EntryID if fail'
BEGIN

IF UserIDIn = (SELECT UserID FROM Expertise WHERE UserID = UserIDIn LIMIT 1) THEN
	SELECT EntryID,ExpertiseName,Details FROM Expertise WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserFullName`(IN `UserIDIn` INT(8))
    NO SQL
BEGIN
IF UserIDIn = (SELECT UserID FROM Mentor WHERE UserID = UserIDIn) THEN
	SELECT concat(FirstName,' ',LastName) AS Name FROM Mentor WHERE UserID = UserIDIn;
ELSEIF UserIDIn = (SELECT UserID FROM Mentee WHERE UserID = UserIDIn) THEN
	SELECT concat(FirstName,' ',LastName) AS Name FROM Mentee WHERE UserID = UserIDIn;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetReportsReviewed`()
    NO SQL
BEGIN

SELECT EntryID,ReporterID,ReportedUserID,Details,ReportDate FROM Reports WHERE IsReviewed = 1;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserID_Username`(IN `UserNameIn` VARCHAR(100))
    NO SQL
    COMMENT '-1 if no user, UserID if exists'
BEGIN
IF UserNameIn = (SELECT UserName FROM User WHERE UserName = UserNameIn) THEN
	SELECT UserID FROM User WHERE UserName = UserNameIn;
ELSE
	SELECT -1 AS UserID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserPassword_UserName`(IN `UserNameIn` VARCHAR(100))
    NO SQL
    COMMENT '-1 as PasswordHash if no user, users PasswordHash if success'
BEGIN

IF UserNameIn = (SELECT UserName FROM User WHERE UserName = UserNameIn) THEN
	SELECT PasswordHash FROM User WHERE UserName = UserNameIn;
ELSE
	SELECT -1 AS PasswordHash;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserPrimaryCommunication`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT 'EntryID,CommunicationName,Details for users PrimaryCommunication, -1 as EntryID if fail'
BEGIN

IF UserIDIn = (SELECT UserID FROM PrimaryCommunication WHERE UserID = UserIDIn LIMIT 1) THEN
	SELECT EntryID,CommunicationName,Details FROM PrimaryCommunication WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserPassword`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT '-1 as PasswordHash if no user, users PasswordHash if success'
BEGIN

IF UserIDIn = (SELECT UserID FROM User WHERE UserID = UserIDIn) THEN
	SELECT PasswordHash FROM User WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS PasswordHash;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetReportsAll`()
    NO SQL
    COMMENT 'Returns all report lines'
BEGIN

SELECT * FROM Reports;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserAvailability`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT 'EntryID,AvailabilityName,Details for users Availibility, -1 as EntryID if fail'
BEGIN

IF UserIDIn = (SELECT UserID FROM MentorAvailability WHERE UserID = UserIDIn LIMIT 1) THEN
	SELECT EntryID,AvailabilityName,Details FROM MentorAvailability WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserWorkExperience`(IN `UserIDIn` INT(8))
    NO SQL
BEGIN

IF UserIDIn = (SELECT UserID FROM WorkExperience WHERE UserID = UserIDIn LIMIT 1) THEN
	SELECT EntryID,CompanyName,JobName,Details FROM WorkExperience WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `ChangeUserPassword`(IN `UserIDIn` INT(8), IN `PasswordIn` VARCHAR(255))
    NO SQL
    COMMENT '-1 as Confirmation if no user, 0 if success'
BEGIN
DECLARE Confirmation int(1) DEFAULT -1;

IF UserIDIn = (SELECT UserID FROM User WHERE UserID = UserIDIn) THEN
	SET Confirmation = 0;
    UPDATE User SET PasswordHash =  PasswordIn WHERE UserID = UserIDIn;
END IF;

SELECT Confirmation;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUserSkills`(IN `UserIDIn` INT(8))
    NO SQL
    COMMENT 'EntryID,SkillName,Details for user skills, -1 as EntryID if fail'
BEGIN

IF UserIDIn = (SELECT UserID FROM Skill WHERE UserID = UserIDIn LIMIT 1) THEN
	SELECT EntryID,SkillName,Details FROM Skill WHERE UserID = UserIDIn;
ELSE
	SELECT -1 AS EntryID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CheckEmailVerification`(IN `EmailIn` VARCHAR(100), IN `HashIn` VARCHAR(255))
    NO SQL
    COMMENT '0=badHash,1=alreadyValidated,2=noEmailFound,3=emailNowValidated'
BEGIN

IF EmailIn = (SELECT Email FROM EmailVerification WHERE Email = EmailIn) THEN
	IF 0 = (SELECT validated FROM EmailVerification WHERE Email = EmailIn) THEN
    	IF HashIn = (SELECT Hash FROM EmailVerification WHERE Email = EmailIn) THEN
        	SELECT 3 AS Confirmation;
            UPDATE EmailVerification SET validated = 1 WHERE Email = EmailIn;
        ELSE
        	SELECT 0 AS Confirmation;
   		END IF;
    ELSE
    	SELECT 1 AS Confirmation;
    END IF;
ELSE
	SELECT 2 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `OLD_VerifyPassword`(IN `UserIDIn` INT, IN `HashIn` BINARY(64))
    NO SQL
    COMMENT '-1 no user found, -2 password mismatch, 0 password match'
BEGIN
DECLARE Confirmation int(1) DEFAULT -1;

IF UserIDIn = (SELECT UserID FROM User WHERE UserID = UserIDIn) THEN
	SET Confirmation = -2;
    IF HashIn = (SELECT PasswordHash FROM User WHERE UserID = UserIDIn) THEN
    	SET Confirmation = 0;
    END IF;
END IF;

SELECT Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `CheckEmailChangeVerification`(IN `EmailIn` VARCHAR(100), IN `HashIn` VARCHAR(255))
    NO SQL
    COMMENT '0=badHash,1=alreadyValidated,2=noEmailFound,3=emailNowValidated'
BEGIN

IF EmailIn = (SELECT OldEmail FROM EmailChangeVerification WHERE OldEmail = EmailIn) THEN
	IF 0 = (SELECT validated FROM EmailChangeVerification WHERE OldEmail = EmailIn) THEN
    	IF HashIn = (SELECT Hash FROM EmailChangeVerification WHERE OldEmail = EmailIn) THEN
        	SELECT 3 AS Confirmation, OldEmail From EmailChangeVerification WHERE OldEmail = EmailIn;
            UPDATE EmailChangeVerification SET validated = 1 WHERE OldEmail = EmailIn;
        ELSE
        	SELECT 0 AS Confirmation;
   		END IF;
    ELSE
    	SELECT 1 AS Confirmation;
    END IF;
ELSE
	SELECT 2 AS Confirmation;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SearchMentors_Details(unfinished)`(IN `Input` VARCHAR(500))
    NO SQL
BEGIN

SELECT UserID FROM WorkExperience WHERE Details LIKE concat('%',Input,'%')
UNION
SELECT UserID FROM UserAffiliation WHERE Details LIKE concat('%',Input,'%');

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `OLD_VerifyUser`(IN `UserNameIn` VARCHAR(100), IN `PasswordHashIn` BINARY(64))
    NO SQL
    COMMENT '-1 if username/password mismatch, UserID if successful'
BEGIN

IF UserNameIn = (SELECT UserName FROM User WHERE UserName = UserNameIN AND PasswordHash = PasswordHashIn) THEN
	SELECT UserID FROM User WHERE UserName = UserNameIn;
ELSE
	SELECT -1 AS UserID;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `GetUsersConversations`(IN `UserIDIn` INT(8))
    NO SQL
BEGIN

SELECT ConversationID FROM ConversationUser WHERE UserID = UserIDIn;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetConversationPending`(IN `ConversationIDIn` INT(8))
    NO SQL
    COMMENT 'same as SetConversationLocked'
BEGIN
DECLARE Confirmation INT;

IF ConversationIDIn = (SELECT ConversationID FROM Conversation WHERE ConversationID = ConversationIDIn) 
THEN
	UPDATE Conversation
    SET status = 1
    WHERE ConversationID = ConversationIDIn;
    SET Confirmation = 0;
ELSE
	SET Confirmation = -1;
END IF;

Select Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetConversationUnreviewed`(IN `ConversationIDIn` INT(8))
    NO SQL
    COMMENT '0 as Confirmation if success, -1 if no conversation found'
BEGIN
DECLARE Confirmation INT;

IF ConversationIDIn = (SELECT ConversationID FROM Conversation WHERE ConversationID = ConversationIDIn) 
THEN
	UPDATE Conversation
    SET status = 2
    WHERE ConversationID = ConversationIDIn;
    SET Confirmation = 0;
ELSE
	SET Confirmation = -1;
END IF;


Select Confirmation;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`jcaraway`@`localhost` PROCEDURE `SetAdminEmailCredentials`(IN `EmailIn` VARCHAR(100), IN `PasswordIn` VARCHAR(100))
    NO SQL
    COMMENT 'Sets the admin Email credentials for automated emails'
BEGIN
DELETE FROM AdminEmailCredential;

INSERT INTO AdminEmailCredential(EmailAddress,Password) VALUE(EmailIn,PasswordIn);

END$$
DELIMITER ;
