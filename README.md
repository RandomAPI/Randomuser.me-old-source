# Randomuser.me Data

### About
Our goal is to have a very diverse database that consists of data unique to different nationalities.
While some places might have an SSN or their phone number might be formatted a certain way, other places usually follow a completely different set of rules.

Help us make the Random User Generator better by contributing to our database and teaching us the proper way of formatting data for different nationalities.

### Guidelines
If you would like to help contribute data specific to a region, please keep these few rules in mind:

1. Please keep all of the data organized.
    - Keep US data in the US directory, AU in the AU directory, etc.

2. No duplicates. Make sure that the data you are adding isn't already on the list.
    - An easy way to remove duplicates from your file and sort: 
```sh
sort -u <file> -o <file>
```

3\. Please don't submit requests that say "make this nationality". We will accept helpful contributions, but not orders :)

### What if I want to add a new nationality?
Go ahead! We will gladly accept new regions if they follow the guidelines above.
Just place your files in a new directory with the appropriate 2 letter ISO Country Code (http://countrycode.org).
And don't worry abut the version number...we'll handle that stuff.

### So how do I contribute?
Send us a pull request with the lists that are unique to the nationality. Things like first/last names, city names, and street names would be considered unique while usernames and generated passwords would be considered something common that is shared between all of the nationalities.

If you have any questions, feel free to ask us on our Twitter page [@randomapi](https://twitter.com/randomapi) or send us an email: support [at] randomapi.com

