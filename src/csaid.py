class BATTLING_EAGLES:    
    def __init__(self,Entry):    
        self.Entry = Entry
        
def Battling_Eagles(): #Enter input   
    Entry = input('Entry: ')
    csaid = Entry
    return csaid

def sum(sheep): #Sum csaid string
    Sum = 0
    while(sheep > 0):
        Remainder = sheep % 10
        Sum = Sum + Remainder
        sheep = sheep //10
    return Sum

def firm(RESOLVE): #Resolve string to 1~9
    firm = 0
    while RESOLVE > 9:
        Marks = RESOLVE % 10
        firm = firm + Marks
        RESOLVE = RESOLVE //10
    return RESOLVE

def presentID():
    CD = Battling_Eagles() #Output
    eagle = len(CD)
    print ("\nYour CSAID (Cat Standard Alpha ID): ", CD)

    CSAID = sum(eagle)
    Command = firm(CSAID)
    print ("You belong to:", Command, "Command")

presentID()
    




