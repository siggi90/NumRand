# NumRand
NumRand generates random numbers from chaotic motion.

NumRand generates random numbers as well as randomizing the order of lists. Each different initial position yields unique results.

NumRand is based on chaotic motion and in that way is different than most pseudo-random number generators. It is computationally more expensive but yields better results.

The initial state of the algorithm is determined by a point in a two-dimensional plane and the offset of 13 cylinders.

No limit is set on the number of digits of the numbers NumRand can generate. 

Getting started:

Run create_databases.php to create databases necessary for NumRand. To change the initial state of the algorithm you can add a new row to the 'state' table, with a new particle position and direction. Keep in mind the particle position must be within the distance of 50 from the center point (0, 0). If the distance is greater the point is out of bounds. Other things that can be changed to alter the state of the algorithm are 'Phase offsets' which can be changed from within code or from the 'cylinders' table. Phase offsets and particle directions must be between 0 and 359 (including 359) NumRand has two functions:

    Generate Random Numbers
    Randomize List

Random function takes as input start value, stop value and the amount of numbers needed and returns an array of random numbers within the start and stop parameters.

$this->random->_random($start, $stop, $amount); //limited to max integer in PHP

$this->random->_random_length(100) //generates string with random digits can generate unlimited number of digits

for more information visit: http://noob.software/support/#index/app_instructions#4

NumRand is also available as NuGet package: https://www.nuget.org/packages/NumRand/
