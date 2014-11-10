using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;

using System.Net;

using System.Runtime.Serialization;
using System.Runtime.Serialization.Json;
using System.IO;

namespace BorneUI
{
    /// <summary>
    /// Interaction logic for MainWindow.xaml
    /// </summary>
    public partial class MainWindow : Window
    {
        public String BaseUrl { get; set; }
        public Borne Borne { get; set; }
        public WebClient client = new WebClient();

        public MainWindow()
        {
            BaseUrl = "http://10.16.162.194";
            InitializeComponent();
        }

        private void Activate(object sender, RoutedEventArgs e)
        {
            Button_Activation.Visibility = System.Windows.Visibility.Hidden;
            if (RequestActivationBorne())
            {
                MessageBox.Show("Borne activée !");
                TextBlock_ID.Text = "Borne n°" + Borne.Id;
            }
        }

        private bool RequestActivationBorne()
        {
            int token = new Random().Next();
            DataContractJsonSerializer js = new DataContractJsonSerializer(typeof(Borne));

            String json = client.DownloadString(BaseUrl + "/api/bornes/register?token=" + token + "&type=borne");

            MemoryStream ms = new MemoryStream(System.Text.ASCIIEncoding.ASCII.GetBytes(json));

            Borne = (Borne)js.ReadObject(ms);
            ms.Close();
            if (Borne.Id.ToString().Length > 0) return true;
            return false;
        }
    }

    [DataContract]
    public class Borne
    {
        [DataMember(Name = "id", IsRequired = true)]
        public int Id { get; set; }

        [DataMember(Name = "token", IsRequired = true)]
        public String Token { get; set; }

        [DataMember(Name = "type", IsRequired = true)]
        public String Type { get; set; }
    }
}
