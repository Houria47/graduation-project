/*
 ** My Own Validator with help of validate.js library
 */
class ValidateIt {
  static MaxPassStrength;
  constructor() {
    this.isValid = true;
    this.passStgFactors = {
      count: false,
      Uletters: false,
      Lletters: false,
      numbers: false,
      special: false,
    };
    this.MaxPassStrength = Object.keys(this.passStgFactors).length;
  }
  isEmpty(value) {
    let res = validate.single(value, { presence: { allowEmpty: false } });
    if (res !== undefined && res !== null) {
      this.isValid = false;
      return true;
    } else {
      return false;
    }
  }
  name(name, msgElement) {
    msgElement = msgElement || $();
    if (this.isEmpty(name)) {
      this.isValid = false;
      msgElement.html("حقل الاسم لا يجب أن يكون فارغ");
      return false;
    }
    // Empty Message Element
    msgElement.html("");
    return true;
  }
  email(email, msgElement) {
    msgElement = msgElement || $();
    if (this.isEmpty(email)) {
      this.isValid = false;
      msgElement.html("حقل البريد لا يجب أن يكون فارغ");
      return false;
    } else {
      let res = validate.single(email, { email: true }) ?? false;
      if (res) {
        this.isValid = false;
        msgElement.html("يرجى إدخال بريد صحيح");
        return false;
      }
    }
    // Empty Message Element
    msgElement.html("");
    return true;
  }
  workHour(open, close, msgElement) {
    msgElement = msgElement || $();
    if (this.isEmpty(open) && this.isEmpty(close)) {
      this.isValid = false;
      msgElement.html("يرجى تحديد ساعات العمل");
      return false;
    } else {
      if (this.isEmpty(open)) {
        this.isValid = false;
        msgElement.html("يرجى تحديد موعد بدء العمل");
        return false;
      }
      if (this.isEmpty(close)) {
        this.isValid = false;
        msgElement.html("يرجى تحديد موعد انتهاء العمل");
        return false;
      }
    }
    if (open >= close) {
      this.isValid = false;
      msgElement.html("موعد بدء العمل يجب أن يكون قبل موعد إنتهاء العمل");
      return false;
    }
    // Empty Message Element
    msgElement.html("");
    return true;
  }
  phone(phoneNum, msgElement) {
    msgElement = msgElement || $();
    if (this.isEmpty(phoneNum)) {
      this.isValid = false;
      msgElement.html("حقل الرقم لا يجب أن يكون فارغ");
      return false;
    }
    if (phoneNum.length != 10 || !/09[3|4|5|6|7|8|9][0-9]{7}/.test(phoneNum)) {
      this.isValid = false;
      msgElement.html("الرقم غير صالح");
      return false;
    }
    // Empty Message Element
    msgElement.html("");
  }
  password(pass, repass, msgElement) {
    msgElement = msgElement || $();
    // TODO: handle spaces in password

    // clear tags
    $(".pass-msg-tags").html("");
    if (this.isEmpty(pass)) {
      this.isValid = false;
      msgElement.html("حقل كلمة المرور لا يجب أن يكون فارغ");
      return false;
    } else {
      if (this.strengthChecker(pass) != this.MaxPassStrength) {
        this.isValid = false;
        msgElement.html(
          " كلمة المرور يجب أن تحوي حرف كبير وحرف صغير ورقم على الأقل وأن تكون بطول 8 محارف أو أكثر "
        );
        return false;
      } else {
        if (pass !== repass) {
          this.isValid = false;
          msgElement.html("كلمات المرور غير متطابقة");
          return false;
        }
      }
    }
    // Empty Message Element
    msgElement.html("");
    return true;
  }
  price(price, discountPrice, priceMsg, discountMsg) {
    priceMsg = priceMsg || $();
    discountMsg = discountMsg || $();
    // clear old messages
    priceMsg.html("");
    discountMsg.html("");
    if (this.isEmpty(price)) {
      this.isValid = false;
      priceMsg.html("حقل السعر لا يجب أن يكون فارغ");
      return false;
    } else {
      if (
        discountPrice.length !== 0 &&
        parseInt(price) <= parseInt(discountPrice)
      ) {
        this.isValid = false;
        discountMsg.html("الحسم يجب أن يكون أقل من السعر");
        return false;
      }
    }
    return true;
  }
  strengthChecker(pass) {
    this.passStgFactors.Uletters = /[A-Z]+/.test(pass) ? true : false;
    this.passStgFactors.Lletters = /[a-z]+/.test(pass) ? true : false;
    this.passStgFactors.numbers = /[0-9]+/.test(pass) ? true : false;
    this.passStgFactors.special = /[!\"$%&/()=?@~`\\.\';:+=^*_-]+/.test(pass)
      ? true
      : false;
    this.passStgFactors.count = pass.length >= 8 ? true : false;

    let strength = Object.values(this.passStgFactors).filter((val) => val);

    return strength.length;
  }
}

function checkStrength(value, strengthMsgEl) {
  let validator = new ValidateIt();
  let strength = validator.strengthChecker(value);
  // empty old messages
  $(".reg-pass-msg").html("");
  let strengthBar = $(".strength");
  strengthMsgEl = strengthMsgEl || $();
  switch (strength) {
    case (0, 1):
      strengthBar.css("width", "0");
      strengthMsgEl.html("Your password is very weak");
      break;
    case 2:
      strengthBar.css("width", "20%");
      strengthBar.css("background-color", "yellow");
      strengthMsgEl.html("Your password is weak");
      break;
    case 3:
      strengthBar.css("width", "50%");
      strengthBar.css("background-color", "orange");
      strengthMsgEl.html("Your password is good");
      break;
    case 4:
      strengthBar.css("width", "75%");
      strengthBar.css("background-color", "limegreen");
      strengthMsgEl.html("Your password is strong");
      break;
    case 5:
      strengthBar.css("width", "100%");
      strengthBar.css("background-color", "green");
      strengthMsgEl.html("Your password is very strong");
      break;
  }

  // Add Password Message Tags

  passMsgTags = $(".pass-msg-tags");

  function addTag(msg) {
    let msgTag = document.createElement("div");
    msgTag.classList.add("pass-msg-tag");
    msgTag.innerHTML = msg;

    passMsgTags.append(msgTag);
  }

  passMsgTags.html("");

  if (validator.passStgFactors.count) addTag("8 أحرف");
  if (validator.passStgFactors.Lletters) addTag("أحرف صغيرة");
  if (validator.passStgFactors.Uletters) addTag("أحرف كبيرة");
  if (validator.passStgFactors.numbers) addTag("أرقام");
  if (validator.passStgFactors.special) addTag("محرف مميز");
}
