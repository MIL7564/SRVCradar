interface ValidatedText {
    firstName: string;
    lastName: string;
    hashtag: string;
    fullText: string;
  }
  
  const validateText = (text: string): ValidatedText => {
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
    if (hashtag.indexOf("#") !== 0) {
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
  
  //Example usage
  const textInput = "John; Doe; #Housing; Need help with rent";
  const validated: ValidatedText = validateText(textInput);
  console.log(validated); // { firstName: "John", lastName: "Doe", hashtag: "#Housing", fullText: "Need help with rent" }