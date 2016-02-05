# Randomuser.me Source

### About
This is the source code that powers the randomuser.me User Generator.

Our goal is to have a very diverse database that consists of data unique to different nationalities.
While some places might have an SSN or their phone number might be formatted a certain way, other places usually follow a completely different set of rules.

Help us make the Random User Generator better by contributing to our database and teaching us the proper way of formatting data for different nationalities.

### How to use the CLI script
`php cli.php <options>`

Available options are
- nat
- results
- seed
- gender
- lego
- format

You tag them along as the 1st argument and seperate each option with a comma.

For example, generating 10 CA female users

`php cli.php results=10,nat=ca,gender=female`

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
Just place your files in a new directory in the `nats` folder with the appropriate 2 letter ISO Country Code (http://countrycode.org). Follow the format of the US folder for reference.

### How to contact us
If you have any questions, feel free to ask us on our Twitter page [@randomapi](https://twitter.com/randomapi).
