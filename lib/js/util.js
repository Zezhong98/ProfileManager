(function loginValidate(emID, pwID) {
  console.log('Login Validating...');
  try {
      addr = $(emID).val();
      pw = $(pwID).val();
      console.log("Validating addr="+addr+" pw="+pw);
      if (addr == null || addr == "" || pw == null || pw == "") {
          alert("Both fields must be filled out");
          return false;
      }
      if ( addr.indexOf('@') == -1 ) {
          alert("Invalid email address");
          return false;
      }
      console.log('pass login validation');
      return true;
  } catch(e) {
      console.log('login validation catch error');
      return false;
  }
  console.log('login validation unknown error');
  return false;
}
)
