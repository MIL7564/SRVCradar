//Delicensed: CC0 1.0 Universal 

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


/* Pseudocode referred to ChatGPT after over 55 experiments in Economics by Salman Shuaib: 
+ Write JavaScript for validating a `Text` ingredient input at IFTTT, such that it is considered VALID if it contains the following FORMAT: 
First Name; Last Name; Hashtag; Full Text
If the VALIDATION fails, the follow content is output:
First Name: "Unknown"; Last Name: "Alerter"; Hashtag: "#Housing", Full Text: `Text`
*/

