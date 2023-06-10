class BATTLING_EAGLES:
    def __init__(self, Entry):
        self.Entry = Entry

def Battling_Eagles(): #Enter input
    Entry = input('Enter first name: ')
    csaid = Entry
    return csaid

def resolute(name):
    initial_value = ord(name[0].lower()) - 96
    while initial_value > 9:
        digits = [int(d) for d in str(initial_value)]
        initial_value = sum(digits)
    return initial_value

def presentID():
    CD = Battling_Eagles() #Output
    name = CD
    print("\nYour name is:", name)

    Command = resolute(name)
    print("You belong to Command", Command)

presentID()
