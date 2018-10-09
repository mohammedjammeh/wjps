# wjps

## Description

Unlike the other applications I have worked on, this project was based on research as it was my final year project. I had to build a Single Sign-On prototype for WJPS which is a web application that accomodates other sub web platforms. In another sense, WJPS required a Single Sign-On authentication mechanism which can allow a user to login to multiple platforms at the same time by logging in once. My responsibility was to find out if this was applicable by WJPS. Therefore, I had to look at different authentication mechanisms and factors regarding web security, and compare them and to find the most suitable one for WJPS. Auth0 has been used as idenity provider using SAML and the 2nd authentication which is shown in the image below, has been built internally using the local database. This means I had to take different security factors into consideration, for example, Cross-Site Request Forgery, Brute Force, Dictionary and several other attacks which could be potentially employed by hacker or unauthorised user.

## Getting Started

First of all, ensure that the two main folders (imperialCollegeHealthcare and sheffieldTeachingHospitals) are separated and treated as individual web folders, not sub folders as commited above. Basically, each folder represents a web application.