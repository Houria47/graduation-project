let Restaurants = {
  accounts: [],
  accountsNum: 0,
  authenticatedAccountsNum: 0,
  verifiedAccountsNum: 0,
  unVerifiedAccountsNum: 0,
  statesPercentage: [],
  fetch: function () {
    return $.ajax({
      method: "GET",
      url: "./../ajax_requests/getRestaurantsAccounts.php",
    }).then((res) => {
      respone = JSON.parse(res);
      if (respone.status) {
        this.accounts = respone.result.restaurants;
        let statistics = respone.result.statistics;
        this.authenticatedAccountsNum = statistics.authenticated;
        this.verifiedAccountsNum = statistics.verified;
        this.accountsNum = this.accounts.length;
        this.unVerifiedAccountsNum =
          this.accountsNum -
          (this.verifiedAccountsNum + this.authenticatedAccountsNum);
        this.statesPercentage = statistics.statesPercentage;
      } else {
        // show moadal with error message
      }
    });
  },
  calcAccountsPercentages: function () {
    // return percentages for accounts according to account status
    let authRes = (this.authenticatedAccountsNum / this.accountsNum) * 100;
    let authenticatedPercentage = Math.trunc(authRes * 10) / 10;
    let verRes = (this.verifiedAccountsNum / this.accountsNum) * 100;
    let verifiedPercentage = Math.trunc(verRes * 10) / 10;
    let unverRes = 100 - (authenticatedPercentage + verifiedPercentage);
    let unVerifiedPercentage = Math.trunc(unverRes * 10) / 10;
    return [authenticatedPercentage, verifiedPercentage, unVerifiedPercentage];
  },
  authenticate: function (restID) {
    return $.ajax({
      method: "POST",
      url: "./../ajax_requests/admin-requests/authenticateRestAccount.php",
      data: { restID: restID },
    }).then((response) => {
      console.log(response);
      data = JSON.parse(response);
      if (data.status) {
        // change status in local array
        let editedAccountIndex = this.accounts.findIndex((account) => {
          return account.id === restID;
        });
        this.accounts[editedAccountIndex].account_status = 2;
      }
      return data;
    });
  },
  delete: function (restID) {
    return $.ajax({
      method: "POST",
      url: "./../ajax_requests/admin-requests/deleteRestAccount.php",
      data: { restID: restID },
    }).then((response) => {
      console.log(response);
      data = JSON.parse(response);
      console.log(data);

      if (data.status) {
        // remove account from local array
        let removedAccountIndex = this.accounts.findIndex(
          (account) => account.id === restID
        );
        this.accounts.splice(removedAccountIndex, 1);
      }
      return data;
    });
  },
};

let Users = {
  accounts: [],
  accountsNum: 0,
  fetch: function () {
    return $.ajax({
      method: "GET",
      url: "./../ajax_requests/admin-requests/getUserAccounts.php",
    }).then((res) => {
      respone = JSON.parse(res);
      if (respone.status) {
        this.accounts = respone.result;
        this.accountsNum = this.accounts.length;
      } else {
        // show moadal with error message
      }
    });
  },
  delete: function (userID) {
    return $.ajax({
      method: "POST",
      url: "./../ajax_requests/admin-requests/deleteUserAccount.php",
      data: { userID },
    }).then((response) => {
      console.log(response);
      data = JSON.parse(response);
      console.log(data);

      if (data.status) {
        // remove account from local array
        let removedAccountIndex = this.accounts.findIndex(
          (account) => account.id === userID
        );
        this.accounts.splice(removedAccountIndex, 1);
      }
      return data;
    });
  },
};
