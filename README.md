I started by adding test coverage for the specification in the pdf as I understood it. `php/tests/GildedRoseTest.php` I evolved this coverage as I went, ensuring it passed for every commit, and exploring any edges cases as I discovered them.

I then implemented the conjured items requirement (interpreted a little creatively to mean any item with the string 'conjured' in its name) by first adding a failing test case, then added an implementation that required very little change to the code.

Once I was satisfied this worked I set about a gradual step by step refactoring of the GildedRose class to untangle the logic, reduce complexity, improve readability and remove repetition. I relied on the test coverage I had added to give me confidence in my changes.

If I had more time, the next steps I would take are:

- Setup github actions to run the test suite on every commit.
- (forbidden in the exercise rules) Rework the domain model significantly so that the data Item objects use timestamps to denote when they came into stock and not incrementing counters. Items could have they're status calculated in real time based on the current date rather needing to run a process each day to increment the counters.
- Introduce an item type system using enums so that logic can be associated with an item's type rather relying on inspecting the string names of items. I hinted at this flexibility in the way I implemented conjured items as multiple possible items rather than one specific item name.

I hope you think it's a good submission, and if not any feedback is welcomed!