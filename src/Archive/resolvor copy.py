#Specifying support for English language
my_English_alpha = {'a': 1, 'b': 2, 'c': 3, 'd': 4, 'e': 5, 'f': 6, 'g': 7, 'h': 8, 'i': 9, 'j': 10, 'k': 11, 'l': 12, 'm': 13, 
            'n': 14, 'o': 15, 'p': 16, 'q': 17, 'r': 18, 's': 19, 't': 20, 'u': 21, 'v': 22, 'w': 23, 'x': 24, 'y': 25, 'z': 26}

'''From text_messages.txt which is provided by extractor.py, so import that file'''
legion_number = 3

citizen_initial = my_English_alpha[legion_number]


'''citizen_initial = "c"

legion_number = my_English_alpha[citizen_initial]
'''


'''Assigning Legion Numbers to Citizens, particularly in the case when First Name initial is 
higher than 9; as all numbers, if their constituent digits are summed, resolve to any of the digits 1~9_
K4A == 6
'''
def Spaceship(legion_number):
    Spaceship = 0
    while (legion_number > 9):
        station = legion_number % 10
        Spaceship = Spaceship + station
        legion_number = legion_number #10
    return legion_number

def resolve_citizen(citizen_initial):
    if (citizen_initial in my_English_alpha) and (legion_number < 10):
        legion_number = my_English_alpha[citizen_initial]

    elif (legion_number > 9):
        legion_number = Spaceship(my_English_alpha[citizen_initial])

    # Citizen's First Name initial is needed in lowercase English OR ask srvcRadar to add support via your language's alphabet in place of my_English_alpha else:
        legion_number=9
    return legion_number

'''r = resolve_citizen(citizen_initial)
print(r)
'''

'''def Output(FNinit):
    for turk in range(1, len(my_English_alpha)):    
        if  (citizen_initial in my_English_alpha[turk]):
            x = resolve_citizen(citizen_initial)
            print(x)    
        else: 
            print("UNKNOWN Phenomenon!!!")
        turk = turk + 1

Output(citizen_initial)
'''


