USE [Backup_eCastEMR_Template]
GO

/****** Object:  Table [dbo].[TabletInput]    Script Date: 02/01/2014 04:40:20 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[TabletInput](
	[TabletInput_ID] [int] IDENTITY(1,1) NOT NULL,
	[Encounter_ID] [int] NOT NULL,
	[TML3_ID] [int] NULL,
	[TML3_Value] [varchar](50) NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

