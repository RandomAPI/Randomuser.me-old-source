Randomuser.me Data
==================
Help us make Random User Generator (RUG) better by contributing to our data!

As of now, RUG only has data pertaining to the US.
We have been working on getting the backend and API ready for the upcoming diversity update, but we are still missing the most important part: the diversity data itself.

That's where you come in!

If you would like to help contribute data specific to a region, please keep these few rules in mind:

1. Please keep all of the data organized.
    - Keep US data in the US directory, AU in the AU directory, etc.
    - Keep filenames consistent across different regions.
2. No duplicates. Make sure that the data you are adding isn't already on the list.
    - An easy way to check for duplicates. 
```sh
sort <file> | uniq -d
```
3. Please re-sort a list if you modify it.
```sh
sort <file> > <file>
```

You may also add new regions if you wish by making a new directory with the appropriate 2 letter ISO Country Code (http://countrycode.org)

And that's about it. If you have any questions, feel free to ask us on our Twitter page @randomapi
