class BATTLING_EAGLES:
    def __init__(self, Entry):
        self.Entry = Entry

def Battling_Eagles(): #Enter input
    Entry = input('Enter first name: ')
    csaid = Entry
    return csaid

def resolute(initial):
    simple_ams_dict = {chr(i): i-96 for i in range(97, 123)}
    return simple_ams_dict.get(initial, 0)

def presentID():
    CD = Battling_Eagles() #Output
    initial = CD[0]
    print("\nYour initial is:", initial)

    Command = resolute(initial)
    print("You belong to Command", Command)

presentID()
