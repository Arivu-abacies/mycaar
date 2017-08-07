MyCAAR
===============================

An enterprise level learning management system developed in YII2 Advanced Application. It mainly comprises of two modules, user module and course module.

User Module:

Implemented with RBAC. Four levels of access

1. User(General user)
2. Assessor (Those who can run tests for user and rate them)
3. Company Admin (Company administrators of each company)
4. SuperAdmin (Top level user, can add companies and perform all actions)

Course Module:

Basic workflow: Programs->courses->lessons->Tests
Tests are of two kinds:

1. Awareness tests (can be taken by users themselves)
2. Capability tests (user should report to an assessor to perform this)

Based on the tests scores, program percentage would be calculated and displayed

Other features includes User enrollements to the programs, email notifications, reports generation, export as excel, archive reports every week, cool search forms and so on!



