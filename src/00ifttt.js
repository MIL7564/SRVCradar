const validateText = (text) => {
    // Split the text into parts using semicolons as separators
    const parts = text.split(";").map(part => part.trim());
    
    // Check that the text has exactly 4 parts
    if (parts.length !== 4) {
      return {
        firstName: "Unknown",
        lastName: "Alerter",
        hashtag: "#Housing",
        fullText: `${text}`,
      };
    }
    
    // Extract the parts and trim any whitespace
    const [firstName, lastName, hashtag, fullText] = parts.map(part => part.trim());
    
    // Check that all parts are non-empty
    if (firstName === "" || lastName === "" || hashtag === "" || fullText === "") {
      return {
        firstName: "Unknown",
        lastName: "Alerter",
        hashtag: "#Housing",
        fullText: `${text}`,
      };
    }
    
    // Check that the hashtag starts with a '#'
    if (!hashtag.startsWith("#")) {
      return {
        firstName: "Unknown",
        lastName: "Alerter",
        hashtag: "#Housing",
        fullText: `${text}`,
      };
    }
    
    // If all checks pass, return the validated parts
    return {
      firstName,
      lastName,
      hashtag,
      fullText,
    };
  };
  
  // Example usage
  const textInput = "Salman Doe; #Housing; Need help with rent";
  const validated = validateText(textInput);
  console.log(validated); // { firstName: "John", lastName: "Doe", hashtag: "#Housing", fullText: "Need help with rent" }
  
//pseudocode: 
/* If Text arrives saying "#housing"
+ has text arrived in 
++ my Mobilephone
++ Or the Googlesheet
+ Where should we intercept it?
+ What is our purpose:
++ Our purpose is putting 9 Commands in competition
+++ Commands are determined based on initial of first name of Alerter_
+ The intercept would have to occur between Mobilephone and IFTTT Service Android SMS; at IFTTT website.
[Music solves the problem of autosequencing of IRRATIONAL Sets of Rules known as Games that we are not yet aware of (Pleasures) by the binding of a Visual NOTE (i.e. an Instrument) 
to one of the 9 Letters. E.g. The empath's friend Chrisitina can represent 3 in her anthropomorphized world]_
++********** This is the INTERCEPT pseudocode for JavaScript:
+++ Validate the format of the received Text_
+++ For the foresaid purpose, the INTERCEPTING JavaScript must validate the FORM of the Alerter's entry (sent Text): 
++++ FORMAT is: "mary morrison #housing spotted an individual near Bay/Bloor intersection"
++++ FORMAT is: "firstname lastname #need description"
++++ If received Text does not match this FOMRAT; then a response Text is sent to Alerter that their Text lacks the standard format: "firstname lastname #need description"_ 
+++ Assign name of Alerter to one of the 9 STANDARDIZING Commands: 1,2,3,4,5,6,7,8,9 based on first inititial of first name_
*/
